<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAudit;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstitutionMessageController extends Controller
{
    public function __construct(
        protected ConversationService $conversationService
    ) {}

    /**
     * Get all conversations for the institution
     */
    public function index(Request $request)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['status' => 'success', 'conversations' => []]);
        }

        $conversations = Conversation::where('institution_id', $institution->id)
            ->with(['porteur:id,name,avatar_url', 'project:id,titre', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'conversations' => $conversations->map(function ($conv) {
                return [
                    'id' => $conv->id,
                    'project_id' => $conv->project_id,
                    'project_title' => $conv->project?->titre,
                    'porteur_name' => $conv->porteur?->name ?? 'Administration',
                    'porteur_avatar' => $conv->porteur?->avatar_url,
                    'last_message' => $conv->messages->first()?->message,
                    'last_message_type' => $conv->messages->first()?->type,
                    'last_message_at' => $conv->last_message_at ? $conv->last_message_at->diffForHumans() : null,
                    'unread_count' => $conv->messages()->where('receiver_id', Auth::id())->where('is_read', false)->count(),
                    'status' => $conv->status,
                ];
            }),
        ]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function show(Request $request, Conversation $conversation)
    {
        // Security check
        if ($conversation->institution_id !== $request->user()->institution?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->with(['sender:id,name,avatar_url', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'porteur' => [
                'name' => $conversation->porteur->name,
                'avatar' => $conversation->porteur->avatar_url,
            ],
            'project' => [
                'title' => $conversation->project->titre,
            ],
        ]);
    }

    /**
     * Send a new message
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|max:10240', // 10MB
            'type' => 'required|in:texte,image,audio,document',
            'reply_to' => 'nullable|exists:messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::findOrFail($request->conversation_id);

        // Security check
        if ($conversation->institution_id !== $request->user()->institution?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'sender_role' => 'institution',
            'message' => $request->message ?? '',
            'type' => $request->type,
            'reply_to' => $request->reply_to,
        ];

        if ($request->hasFile('file')) {
            $upload = $this->conversationService->handleUpload($request->file('file'), $request->user()->id);
            $data['file_path'] = $upload['file_path'];
            $data['mime_type'] = $upload['mime_type'];

            // If it's an image but type was generic document, fix it
            if (str_contains($upload['mime_type'], 'image') && $data['type'] === 'document') {
                $data['type'] = 'image';
            }

            if (! $data['message']) {
                $data['message'] = 'Fichier: '.$request->file('file')->getClientOriginalName();
            }
        }

        $message = $this->conversationService->sendMessage($data);

        return response()->json([
            'status' => 'success',
            'message' => $message->load(['sender:id,name,avatar_url', 'attachments']),
        ], 201);
    }

    /**
     * Start a conversation for a project (or return existing one)
     */
    public function start(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution not found'], 404);
        }

        $project = \App\Models\Project::findOrFail($request->project_id);

        // Check if conversation already exists
        $conversation = \App\Models\Conversation::where('project_id', $project->id)
            ->where('institution_id', $institution->id)
            ->first();

        if (! $conversation) {
            $conversation = \App\Models\Conversation::create([
                'project_id' => $project->id,
                'institution_id' => $institution->id,
                'porteur_id' => $project->user_id,
                'created_by' => $request->user()->id,
                'type' => 'porteur_institution',
                'status' => 'active',
                'last_message_at' => now(),
            ]);

            // Send a neutral first message (not "validated" since discussion may start earlier)
            $this->conversationService->sendMessage([
                'conversation_id' => $conversation->id,
                'sender_id' => $request->user()->id,
                'sender_role' => 'institution',
                'message' => 'Bonjour, nous ouvrons une discussion à propos du projet "'.$project->titre.'".',
                'type' => 'texte',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'conversation' => [
                'id' => $conversation->id,
                'project_id' => $conversation->project_id,
            ],
        ]);
    }

    /**
     * Upload a file to a conversation (separate endpoint for file-first flows)
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
            'file' => 'required|file|max:10240',
            'type' => 'nullable|in:texte,image,audio,document',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::findOrFail($request->conversation_id);

        if ($conversation->institution_id !== $request->user()->institution?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (! $request->hasFile('file')) {
            return response()->json(['errors' => ['file' => 'Aucun fichier fourni.']], 422);
        }

        $upload = $this->conversationService->handleUpload($request->file('file'), $request->user()->id);
        $type = $request->type ?? 'document';

        if (str_contains($upload['mime_type'], 'image') && $type === 'document') {
            $type = 'image';
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'sender_role' => 'institution',
            'message' => 'Fichier: '.$request->file('file')->getClientOriginalName(),
            'type' => $type,
            'file_path' => $upload['file_path'],
            'mime_type' => $upload['mime_type'],
        ];

        $message = $this->conversationService->sendMessage($data);

        return response()->json([
            'status' => 'success',
            'message' => $message->load(['sender:id,name,avatar_url', 'attachments']),
        ], 201);
    }

    /**
     * Search conversations
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $institution = $request->user()->institution;

        if (! $institution) {
            return response()->json(['status' => 'error', 'message' => 'Profil institution non trouvé.'], 404);
        }

        $conversations = Conversation::where('institution_id', $institution->id)
            ->where(function ($q) use ($query) {
                $q->whereHas('porteur', function ($sq) use ($query) {
                    $sq->where('name', 'LIKE', "%{$query}%");
                })->orWhereHas('project', function ($sq) use ($query) {
                    $sq->where('titre', 'LIKE', "%{$query}%");
                });
            })
            ->with(['porteur:id,name,avatar_url', 'project:id,titre'])
            ->get();

        return response()->json([
            'status' => 'success',
            'conversations' => $conversations,
        ]);
    }

    /**
     * Mark a conversation as read
     */
    public function markRead(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        // Security check
        if ($conversation->institution_id !== $request->user()->institution?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Edit a message
     */
    public function editMessage(Request $request, $messageId)
    {
        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::findOrFail($messageId);
        $conversation = $message->conversation;

        if ($conversation->institution_id !== $request->user()->institution?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prevent editing if receiver has replied
        $hasReplies = Message::where('conversation_id', $conversation->id)
            ->where('id', '>', $message->id)
            ->where('sender_id', '!=', $message->sender_id)
            ->exists();

        if ($hasReplies) {
            return response()->json(['error' => 'Impossible de modifier un message déjà répondu'], 422);
        }

        $oldContent = $message->message;
        $message->update([
            'message' => $data['message'],
            'is_edited' => true,
        ]);

        MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'edited',
            'performed_by' => $request->user()->id,
            'details' => 'Message edited by institution. Old: "'.substr($oldContent, 0, 100).'"',
        ]);

        return response()->json(['status' => 'success', 'data' => $message->fresh()]);
    }

    /**
     * Delete a message (soft delete)
     */
    public function deleteMessage(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        $conversation = $message->conversation;

        if ($conversation->institution_id !== $request->user()->institution?->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (! $message->canDelete()) {
            return response()->json(['error' => 'Ce message ne peut pas être supprimé'], 422);
        }

        $message->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'deleted',
            'performed_by' => $request->user()->id,
            'details' => 'Message soft deleted by institution',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Message supprimé']);
    }
}
