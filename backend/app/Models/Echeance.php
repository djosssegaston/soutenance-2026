<?php

namespace App\Models;

use App\Enums\EcheanceStatus;
use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    protected $fillable = [
        'financement_id',
        'project_id',
        'institution_id',
        'numero_echeance',
        'montant_capital',
        'montant_interets',
        'montant_frais',
        'montant_total',
        'montant_paye',
        'montant_restant',
        'date_echeance',
        'date_paiement',
        'statut',
        'penalites',
        'methode_paiement',
        'transaction_reference',
        'notes',
    ];

    protected $casts = [
        'montant_capital' => 'decimal:2',
        'montant_interets' => 'decimal:2',
        'montant_frais' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2',
        'penalites' => 'decimal:2',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
    ];

    public function statutEnum(): ?EcheanceStatus
    {
        return EcheanceStatus::tryFrom($this->statut);
    }

    public function funding()
    {
        return $this->belongsTo(Funding::class, 'financement_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function getStatutLabelAttribute(): string
    {
        return $this->statutEnum()?->label() ?? $this->statut;
    }

    public function scopePending($query)
    {
        return $query->whereIn('statut', [
            EcheanceStatus::PENDING->value,
            EcheanceStatus::UPCOMING->value,
        ]);
    }

    public function scopePaid($query)
    {
        return $query->where('statut', EcheanceStatus::PAID->value);
    }

    public function scopeOverdue($query)
    {
        return $query->where('statut', EcheanceStatus::OVERDUE->value);
    }

    public function isPayee(): bool
    {
        return $this->statutEnum()?->isPayee() ?? false;
    }

    public function isEnRetard(): bool
    {
        return $this->statutEnum()?->isEnRetard() ?? false;
    }
}
