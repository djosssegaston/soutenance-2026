<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'institution_id',
        'type',
        'amount',
        'currency',
        'status',
        'fedapay_transaction_id',
        'fedapay_payment_method',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Relation avec le projet
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'institution
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Scope: transactions complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: transactions en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: filtrer par type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Vérifier si la transaction est complétée
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Marquer la transaction comme complétée
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }
}
