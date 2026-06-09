<?php

namespace App\Models;

use App\Enums\FundingStatus;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'secteur',
        'branche',
        'montant_demande',
        'montant_finance',
        'duree',
        'localisation',
        'statut',
    ];

    protected $casts = [
        'montant_demande' => 'decimal:2',
        'montant_finance' => 'decimal:2',
    ];

    public function statusEnum(): ProjectStatus
    {
        return ProjectStatus::fromStorageOr($this->statut);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function validations()
    {
        return $this->hasMany(ProjectValidation::class);
    }

    public function analyses()
    {
        return $this->hasMany(InstitutionAnalysis::class);
    }

    public function financements()
    {
        return $this->hasMany(Funding::class);
    }

    public function remboursements()
    {
        return $this->hasMany(Repayment::class);
    }

    public function repayments()
    {
        return $this->remboursements();
    }

    public function statusHistories()
    {
        return $this->hasMany(ProjectStatusHistory::class);
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class);
    }

    public function echeances()
    {
        return $this->hasMany(Echeance::class);
    }

    public function financementActif()
    {
        return $this->hasOne(Funding::class)->whereIn('statut', [
            FundingStatus::DISBURSED->value,
            FundingStatus::ACTIVE->value,
        ]);
    }

    public function aUnFinancementActif(): bool
    {
        return $this->financements()
            ->whereIn('statut', [
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
                FundingStatus::APPROVED->value,
            ])
            ->exists();
    }

    public function financementPrecedentTermine(): bool
    {
        return ! $this->financements()
            ->whereIn('statut', [
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
                FundingStatus::APPROVED->value,
                FundingStatus::AWAITING_BORROWER_PLAN->value,
                FundingStatus::AWAITING_IMF_VALIDATION->value,
                FundingStatus::PROPOSED->value,
            ])
            ->exists();
    }

    public function financementActifOuEnCours(): bool
    {
        return $this->financements()
            ->whereIn('statut', [
                FundingStatus::PROPOSED->value,
                FundingStatus::AWAITING_BORROWER_PLAN->value,
                FundingStatus::AWAITING_IMF_VALIDATION->value,
                FundingStatus::APPROVED->value,
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
            ])
            ->exists();
    }

    public function peutEtreFinance(): bool
    {
        $disallowed = [
            ProjectStatus::DRAFT->value,
            ProjectStatus::SUBMITTED->value,
            ProjectStatus::UNDER_ADMIN_REVIEW->value,
            ProjectStatus::ADMIN_REJECTED->value,
            ProjectStatus::INSTITUTION_REJECTED->value,
            ProjectStatus::FUNDED->value,
            ProjectStatus::ACTIVE->value,
            ProjectStatus::CANCELLED->value,
        ];

        return ! in_array($this->statut, $disallowed, true);
    }

    public function isFinanced(): bool
    {
        return $this->statusEnum()->isFinanced();
    }
}
