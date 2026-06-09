<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Institution;
use App\Models\Message;
use App\Models\MessageAudit;
use App\Models\Project;
use App\Models\RealtimeNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * Create a conversation for a project and institution.
     * Rule: Created only when institution validates (accepts) a project.
     */
    public function createForProject(Project $project, Institution $institution, User $creator): Conversation
    {
        return DB::transaction(function () use ($project, $institution, $creator) {
            // Check if exists
            $conversation = Conversation::where('project_id', $project->id)
                ->where('institution_id', $institution->id)
                ->first();

            if ($conversation) {
                return $conversation;
            }

            $conversation = Conversation::create([
                'project_id' => $project->id,
                'institution_id' => $institution->id,
                'porteur_id' => $project->user_id,
                'created_by' => $creator->id,
                'type' => 'porteur_institution',
                'status' => 'active',
                'last_message_at' => now(),
            ]);

            // First message
            $this->sendMessage([
                'conversation_id' => $conversation->id,
                'sender_id' => $creator->id,
                'sender_role' => 'institution',
                'message' => 'Félicitations ! Nous avons validé votre projet "'.$project->titre.'". Nous ouvrons cette discussion pour finaliser les modalités de financement.',
                'type' => 'texte',
            ]);

            return $conversation;
        });
    }

    /**
     * Send a message in a conversation
     */
    public function sendMessage(array $data): Message
    {
        return DB::transaction(function () use ($data) {
            $conversation = Conversation::findOrFail($data['conversation_id']);

            // Determine receiver
            $receiverId = ($data['sender_id'] == $conversation->porteur_id)
                ? $conversation->institution->user_id
                : $conversation->porteur_id;

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $data['sender_id'],
                'receiver_id' => $receiverId,
                'sender_role' => $data['sender_role'],
                'type' => $data['type'] ?? 'texte',
                'message' => $data['message'] ?? '',
                'file_path' => $data['file_path'] ?? null,
                'mime_type' => $data['mime_type'] ?? null,
                'audio_duration' => $data['audio_duration'] ?? null,
                'reply_to' => $data['reply_to'] ?? null,
                'is_read' => false,
            ]);

            // Update conversation timestamp
            $conversation->update(['last_message_at' => now()]);

            // Create realtime notification for receiver
            $this->notifyReceiver($message);

            // Create audit trail
            MessageAudit::create([
                'message_id' => $message->id,
                'action' => 'sent',
                'performed_by' => $data['sender_id'],
                'details' => 'Message sent by '.($data['sender_role'] ?? 'unknown'),
            ]);

            return $message;
        });
    }

    /**
     * Notify the receiver of a new message
     */
    protected function notifyReceiver(Message $message): void
    {
        RealtimeNotification::create([
            'user_id' => $message->receiver_id,
            'type' => 'new_message',
            'title' => 'Nouveau message',
            'message' => 'Vous avez reçu un nouveau message concernant le projet '.$message->conversation->project->titre,
            'data' => [
                'conversation_id' => $message->conversation_id,
                'message_id' => $message->id,
                'sender_name' => $message->sender->name,
            ],
            'is_read' => false,
        ]);

        // Note: Real-time broadcasting via events would be triggered here if configured
        // event(new \App\Events\MessageSent($message));
    }

    /**
     * Handle file uploads for messages
     */
    public function handleUpload($file, $userId): array
    {
        $path = $file->store('messages/'.$userId, 'public');

        return [
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }
}
