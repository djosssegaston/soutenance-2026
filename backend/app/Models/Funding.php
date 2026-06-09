<?php

namespace App\Models;

use App\Enums\EcheanceStatus;
use App\Enums\FundingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funding extends Model
{
    use HasFactory;

    protected $table = 'financements';

    protected $fillable = [
        'project_id',
        'institution_id',
        'porteur_id',
        'montant',
        'montant_demande',
        'montant_propose',
        'montant_valide',
        'montant_decaisse',
        'taux_interet',
        'duree',
        'statut',
        'date_financement',
        'date_validation',
        'date_decaissement',
        'commentaires',
        'montant_mensuel',
        'jour_remboursement',
        'conditions',
        'frais',
        'commentaire_plan',
        'validated_by_porteur_id',
        'date_acceptation_porteur',
        'date_approbation_imf',
        'date_rejet_imf',
        'motif_rejet_imf',
        'echeances_generees',
        'date_cloture',
    ];

    protected $casts = [
        'montant_demande' => 'decimal:2',
        'montant_propose' => 'decimal:2',
        'montant_valide' => 'decimal:2',
        'montant_decaisse' => 'decimal:2',
        'montant_mensuel' => 'decimal:2',
        'taux_interet' => 'decimal:2',
        'date_financement' => 'date',
        'date_validation' => 'datetime',
        'date_decaissement' => 'datetime',
        'date_acceptation_porteur' => 'datetime',
        'date_approbation_imf' => 'datetime',
        'date_rejet_imf' => 'datetime',
        'date_cloture' => 'datetime',
    ];

    public function statutEnum(): ?FundingStatus
    {
        return FundingStatus::tryFrom($this->statut);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function porteur()
    {
        return $this->belongsTo(User::class, 'porteur_id');
    }

    public function histories()
    {
        return $this->hasMany(FinancementHistory::class, 'financement_id');
    }

    public function documents()
    {
        return $this->hasMany(FinancementDocument::class, 'financement_id');
    }

    public function echeances()
    {
        return $this->hasMany(Echeance::class, 'financement_id');
    }

    public function echeancesPayees()
    {
        return $this->echeances()->where('statut', EcheanceStatus::PAID->value);
    }

    public function echeancesEnAttente()
    {
        return $this->echeances()->whereIn('statut', [
            EcheanceStatus::PENDING->value,
            EcheanceStatus::UPCOMING->value,
        ]);
    }

    public function echeancesEnRetard()
    {
        return $this->echeances()->where('statut', EcheanceStatus::OVERDUE->value);
    }

    public function validateurPorteur()
    {
        return $this->belongsTo(User::class, 'validated_by_porteur_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return $this->statutEnum()?->label() ?? $this->statut;
    }

    public function isActif(): bool
    {
        return $this->statutEnum()?->isActive() ?? false;
    }

    public function isTerminee(): bool
    {
        return $this->statutEnum()?->isTerminal() ?? false;
    }

    public function getMontantTotalARembourserAttribute(): float
    {
        return (float) ($this->montant_valide ?: $this->montant_propose) + $this->getInteretsTotalAttribute();
    }

    public function getInteretsTotalAttribute(): float
    {
        $capital = (float) ($this->montant_valide ?: $this->montant_propose);
        $taux = (float) $this->taux_interet;
        $duree = (int) ($this->duree ?: 1);

        return round($capital * ($taux / 100) * ($duree / 12), 2);
    }

    public function getProgressionRemboursementAttribute(): float
    {
        $totalEcheances = $this->echeances()->count();
        if ($totalEcheances === 0) {
            return 0;
        }
        $payees = $this->echeancesPayees()->count();

        return round(($payees / $totalEcheances) * 100, 2);
    }

    public function getResteDuAttribute(): float
    {
        $total = (float) $this->echeances()->sum('montant_total');
        $paye = (float) $this->echeancesPayees()->sum('montant_total');

        return round($total - $paye, 2);
    }

    public function scopeActif($query)
    {
        return $query->whereIn('statut', [
            FundingStatus::DISBURSED->value,
            FundingStatus::ACTIVE->value,
        ]);
    }

    public function scopeTermine($query)
    {
        return $query->whereIn('statut', [
            FundingStatus::COMPLETED->value,
            FundingStatus::DEFAULTED->value,
            FundingStatus::REJECTED->value,
        ]);
    }

    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', [
            FundingStatus::PROPOSED->value,
            FundingStatus::AWAITING_BORROWER_PLAN->value,
            FundingStatus::AWAITING_IMF_VALIDATION->value,
            FundingStatus::APPROVED->value,
            FundingStatus::DISBURSED->value,
            FundingStatus::ACTIVE->value,
        ]);
    }
}
