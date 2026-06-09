<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Delete a message (soft delete)
    public function destroy(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if (! $message->conversation->canAccess($request->user()->id)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if (! $message->canDelete()) {
            return response()->json(['error' => 'Ce message ne peut pas être supprimé'], 422);
        }

        $message->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        \App\Models\MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'deleted',
            'performed_by' => $request->user()->id,
            'details' => 'Message soft deleted by '.$request->user()->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Message supprimé']);
    }

    // Edit a message
    public function update(Request $request, $messageId)
    {
        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::findOrFail($messageId);

        if (! $message->conversation->canAccess($request->user()->id)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Prevent editing if message has been replied to
        $hasReplies = Message::where('conversation_id', $message->conversation_id)
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

        \App\Models\MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'edited',
            'performed_by' => $request->user()->id,
            'details' => 'Message edited. Old: "'.substr($oldContent, 0, 100).'"',
        ]);

        return response()->json(['success' => true, 'data' => $message->fresh()]);
    }

    // Send a message (text or file)
    public function store(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
            'message' => 'nullable|string|max:5000',
            'type' => 'sometimes|in:texte,image,audio,document',
            'file' => 'nullable|file|max:20480', // 20MB max
        ]);

        $conversation = Conversation::findOrFail($data['conversation_id']);

        // Check access
        if (! $conversation->canAccess($request->user()->id)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $user = $request->user();
        $type = $data['type'] ?? 'texte';
        $filePath = null;
        $messageContent = $data['message'] ?? '';

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Auto-detect type from mime if not explicitly set
            if ($type === 'texte') {
                $mime = $file->getMimeType();
                if (str_starts_with($mime, 'image/')) {
                    $type = 'image';
                } elseif (str_starts_with($mime, 'audio/')) {
                    $type = 'audio';
                } else {
                    $type = 'document';
                }
            }

            // Validate file type based on message type
            $allowedExtensions = [
                'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                'audio' => ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'],
                'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'],
            ];

            if (isset($allowedExtensions[$type]) && ! in_array($extension, $allowedExtensions[$type])) {
                return response()->json(['error' => 'Type de fichier non autorisé'], 422);
            }

            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('messages/'.$user->id, $fileName, 'public');

            if (! $messageContent) {
                $messageContent = 'Fichier: '.$file->getClientOriginalName();
            }
        }

        // Determine receiver: the other party in the conversation
        if ($user->id === $conversation->porteur_id) {
            $receiverId = $conversation->institution?->user_id;
        } else {
            $receiverId = $conversation->porteur_id ?? $conversation->institution?->user_id;
        }

        $message = Message::create([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'sender_role' => $user->role,
            'type' => $type,
            'message' => $messageContent,
            'file_path' => $filePath,
            'is_read' => false,
            'created_at' => now(),
        ]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        // Create message audit trail
        \App\Models\MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'sent',
            'performed_by' => $user->id,
            'details' => 'Message sent by '.$user->name.' ('.$user->role.')',
        ]);

        // Send notification to the other party
        if ($receiverId) {
            \App\Models\UserNotification::notify(
                $receiverId,
                'message',
                'Nouveau message',
                'Vous avez un nouveau message de '.$user->name
            );
        }

        return response()->json(['message' => $message->load('sender')], 201);
    }

    // Mark message as read
    public function markAsRead(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        $conversation = $message->conversation;

        if (! $conversation->canAccess($request->user()->id)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }
}
