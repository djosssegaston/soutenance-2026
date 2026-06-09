<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'sender_role',
        'type', // texte, image, audio, document
        'message',
        'file_path',
        'mime_type',
        'audio_duration',
        'is_read',
        'is_edited',
        'is_deleted',
        'reply_to',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function audits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageAudit::class);
    }

    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'reply_to');
    }

    // Check if message can be deleted (no replies exist and not a proof)
    public function canDelete(): bool
    {
        if ($this->is_deleted) {
            return false;
        }

        // Critical Rule: cannot delete if receiver already replied
        $hasReplies = $this->conversation->messages()
            ->where('id', '>', $this->id)
            ->where('sender_id', '!=', $this->sender_id)
            ->exists();

        if ($hasReplies) {
            return false;
        }

        // Cannot delete if used in dispute or financial proof (simplified check for now)
        // In real app, check Dispute model or FinancementHistory
        return true;
    }

    // Check if message can be edited
    public function canEdit(): bool
    {
        return $this->canDelete();
    }

    // Mark as read
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }
}
