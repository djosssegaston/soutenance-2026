<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAudit;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function start(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:porteur,institution',
            'target_id' => 'required|integer|exists:users,id',
        ]);

        $user = $request->user();
        $target = User::findOrFail($data['target_id']);

        // Check if conversation already exists between admin and this user
        $existing = Conversation::where('created_by', $user->id)
            ->where(function ($q) use ($target) {
                $q->where('porteur_id', $target->id);
                if ($institution = $target->institution) {
                    $q->orWhere('institution_id', $institution->id);
                }
            })
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'conversation_id' => $existing->id,
                'name' => $target->name,
            ]);
        }

        $conversation = Conversation::create([
            'type' => 'admin_support',
            'status' => 'active',
            'created_by' => $user->id,
            'porteur_id' => $data['type'] === 'porteur' ? $target->id : null,
            'institution_id' => $data['type'] === 'institution' ? $target->institution?->id : null,
            'last_message_at' => now(),
        ]);

        $firstMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $target->id,
            'sender_role' => 'admin',
            'type' => 'texte',
            'message' => 'Conversation initiée par l\'administration.',
            'is_read' => false,
        ]);

        MessageAudit::create([
            'message_id' => $firstMessage->id,
            'action' => 'sent',
            'performed_by' => $user->id,
            'details' => 'Admin started conversation with '.$target->name,
        ]);

        UserNotification::notify(
            $target->id,
            'message',
            'Nouvelle conversation',
            'L\'administration a ouvert une conversation avec vous.'
        );

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'name' => $target->name,
        ]);
    }

    public function archive($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->update(['status' => 'archived']);

        MessageAudit::create([
            'message_id' => $conversation->messages()->first()?->id ?? 0,
            'action' => 'archived',
            'performed_by' => auth()->id(),
            'details' => 'Conversation #'.$id.' archived by admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Conversation archivée.']);
    }

    public function conversations()
    {
        $conversations = Conversation::with(['project', 'institution', 'porteur', 'latestMessage'])
            ->where('status', 'active')
            ->latest('last_message_at')
            ->get();

        $unreadTotal = 0;
        $repliedToday = 0;

        $list = $conversations->map(function ($c) use (&$unreadTotal, &$repliedToday) {
            $unread = $c->messages()->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
            $unreadTotal += $unread;

            $lm = $c->latestMessage()->first();
            if ($lm && $lm->created_at?->isToday()) {
                $repliedToday++;
            }

            return [
                'id' => $c->id,
                'name' => $c->porteur?->name ?? ($c->institution?->nom ?? 'N/A'),
                'avatar' => $c->porteur?->avatar_url ?? $c->institution?->logo ?? null,
                'last_message' => $lm ? substr($lm->message, 0, 80) : '',
                'last_at' => $lm?->created_at?->diffForHumans() ?? '',
                'unread' => $unread,
            ];
        });

        return response()->json([
            'success' => true,
            'stats' => [
                'unread' => $unreadTotal,
                'total' => $list->count(),
                'replied_today' => $repliedToday,
                'avg_response' => '—',
            ],
            'conversations' => $list,
        ]);
    }

    public function messages($id)
    {
        $conversation = Conversation::findOrFail($id);

        $msgs = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        // Mark messages as read for admin
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->update(['is_read' => true]);

        $data = $msgs->map(fn ($m) => [
            'id' => $m->id,
            'content' => $m->message,
            'message' => $m->message,
            'sender_id' => $m->sender_id,
            'sender_role' => $m->sender_role,
            'type' => $m->type,
            'file_path' => $m->file_path,
            'mime_type' => $m->mime_type,
            'is_read' => (bool) $m->is_read,
            'is_edited' => (bool) $m->is_edited,
            'is_deleted' => (bool) $m->is_deleted,
            'created_at' => $m->created_at,
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function reply(Request $request, $id)
    {
        $data = $request->validate([
            'content' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|max:20480',
            'type' => 'sometimes|in:texte,image,audio,document',
        ]);

        $conversation = Conversation::findOrFail($id);
        $user = $request->user();

        $type = 'texte';
        $filePath = null;
        $messageContent = $data['content'] ?? '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mime = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());
            $type = match (true) {
                isset($data['type']) && $data['type'] !== 'texte' => $data['type'],
                str_starts_with($mime, 'image/') => 'image',
                str_starts_with($mime, 'audio/') => 'audio',
                default => 'document',
            };
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('messages/'.$user->id, $fileName, 'public');
            if (! $messageContent) {
                $messageContent = 'Fichier: '.$file->getClientOriginalName();
            }
        }

        $receiverId = $conversation->porteur_id
            ?? $conversation->institution?->user_id
            ?? null;

        $message = Message::create([
            'conversation_id' => $id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'sender_role' => 'admin',
            'type' => $type,
            'message' => $messageContent,
            'file_path' => $filePath,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'sent',
            'performed_by' => $user->id,
            'details' => 'Admin replied to conversation #'.$id,
        ]);

        if ($receiverId) {
            UserNotification::notify(
                $receiverId,
                'message',
                'Nouveau message de l\'administration',
                'Vous avez reçu un message de l\'administration concernant votre projet.'
            );
        }

        return response()->json(['success' => true, 'message' => 'Message envoyé.', 'data' => $message->fresh()]);
    }

    public function editMessage(Request $request, $messageId)
    {
        $data = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message = Message::findOrFail($messageId);
        $user = $request->user();

        // Authorization: admin can edit only if no reply exists
        if (! $message->canEdit()) {
            return response()->json(['error' => 'Ce message ne peut plus être modifié (réponse existante).'], 422);
        }

        $oldContent = $message->message;
        $message->update([
            'message' => $data['content'],
            'is_edited' => true,
        ]);

        MessageAudit::create([
            'message_id' => $message->id,
            'action' => 'edited',
            'performed_by' => $user->id,
            'details' => 'Message edited by admin. Old: "'.substr($oldContent, 0, 100).'"',
        ]);

        return response()->json(['success' => true, 'data' => $message->fresh()]);
    }

    public function deleteMessage(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);

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
            'details' => 'Message deleted by admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Message supprimé']);
    }
}
