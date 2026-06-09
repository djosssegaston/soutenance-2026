<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // Get all conversations for the porteur
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'porteur') {
            return response()->json(['conversations' => []]);
        }

        $conversations = Conversation::with(['project', 'institution', 'latestMessage'])
            ->where('porteur_id', $user->id)
            ->where('status', 'active')
            ->latest('last_message_at')
            ->get();

        $userId = $user->id;
        $formatted = $conversations->map(function ($conv) use ($userId) {
            $latestMessage = $conv->latestMessage()->first();

            return [
                'id' => $conv->id,
                'project' => [
                    'id' => $conv->project?->id,
                    'titre' => $conv->project?->titre ?? 'N/A',
                ],
                'institution' => [
                    'id' => $conv->institution?->id,
                    'nom' => $conv->type === 'admin_support' ? 'Administration' : ($conv->institution?->nom ?? 'N/A'),
                    'logo' => $conv->type === 'admin_support' ? null : ($conv->institution?->logo ?? null),
                ],
                'last_message' => $latestMessage ? [
                    'content' => substr($latestMessage->message, 0, 50),
                    'created_at' => $latestMessage->created_at?->format('H:i'),
                    'is_read' => (bool) $latestMessage->is_read,
                ] : null,
                'unread_count' => (int) $conv->messages()
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count(),
            ];
        });

        return response()->json(['conversations' => $formatted]);
    }

    // Get messages for a specific conversation (web session version)
    public function messages(Request $request, $conversationId)
    {
        $user = $request->user();
        $conversation = Conversation::findOrFail($conversationId);

        // Check access
        if ($conversation->porteur_id !== $user->id &&
            ($conversation->institution_id === null || $conversation->institution?->user_id !== $user->id)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->paginate(50);

        // Mark messages as read (only for recipient)
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
            ],
        ]);
    }

    // Send a new message (web session version)
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'message' => ['required', 'string'],
            'type' => ['sometimes', 'in:texte,file'],
        ]);

        $conversation = Conversation::findOrFail($data['conversation_id']);
        $user = $request->user();

        // Check access
        if ($conversation->porteur_id !== $user->id &&
            ($conversation->institution_id === null || $conversation->institution?->user_id !== $user->id)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $message = Message::create([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $user->id,
            'sender_role' => $user->role,
            'message' => $data['message'],
            'type' => $data['type'] ?? 'texte',
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'message' => 'Message envoyé.',
            'data' => $message->load('sender'),
        ]);
    }
}
