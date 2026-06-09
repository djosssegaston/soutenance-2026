<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'project_id',
        'institution_id',
        'porteur_id',
        'created_by',
        'type',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function porteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'porteur_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    // Scopes
    public function scopeForPorteur($query, $userId)
    {
        return $query->where('porteur_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Business Logic: Create conversation when institution validates project
    public static function createForProject(Project $project, Institution $institution): self
    {
        $conversation = self::create([
            'project_id' => $project->id,
            'institution_id' => $institution->id,
            'porteur_id' => $project->user_id,
            'type' => 'porteur_institution',
            'status' => 'active',
            'last_message_at' => now(),
        ]);

        // Create first message from institution
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $institution->user_id,
            'sender_role' => 'institution',
            'message' => 'Bonjour, nous avons validé votre projet "'.$project->titre.'". Nous ouvrons cette discussion pour échanger.',
            'type' => 'texte',
            'is_read' => false,
        ]);

        return $conversation;
    }

    // Check if user can access this conversation
    public function canAccess($userId): bool
    {
        return $this->porteur_id === $userId ||
               $this->institution?->user_id === $userId;
    }
}
