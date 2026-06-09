<?php

namespace App\Enums;

enum ProjectStatus: string
{
    // États initiaux
    case DRAFT = 'draft';                      // Brouillon
    case SUBMITTED = 'submitted';              // Soumis par le porteur

    // États de validation admin
    case UNDER_ADMIN_REVIEW = 'under_admin_review';  // En révision admin
    case ADMIN_REJECTED = 'admin_rejected';           // Rejeté par admin
    case ADMIN_VALIDATED = 'admin_validated';         // Validé par admin
    case AVAILABLE_FOR_IMF = 'available_for_imf';     // Disponible pour IMF

    // États d'analyse institution
    case UNDER_INSTITUTION_REVIEW = 'under_institution_review';  // En analyse institution
    case INTERVIEW_SCHEDULED = 'interview_scheduled';           // Entretien planifié
    case INTERVIEW_CONFIRMED = 'interview_confirmed';           // Entretien confirmé
    case DOCUMENTS_REQUESTED = 'documents_requested';           // Documents demandés
    case INSTITUTION_REJECTED = 'institution_rejected';         // Rejeté par institution

    // États de financement
    case INSTITUTION_ACCEPTED = 'institution_accepted';  // Accepté par institution
    case FINANCING_PENDING = 'financing_pending';        // En attente de validation financement
    case FUNDED = 'funded';                              // Financé
    case ACTIVE = 'active';                              // En cours de remboursement
    case REPAID = 'repaid';                              // Remboursé (toutes échéances payées)
    case CLOSED = 'closed';                              // Clôturé

    // États finaux
    case SUSPENDED = 'suspended';        // Suspendu par admin
    case COMPLETED = 'completed';    // Terminé (remboursements complets)
    case CANCELLED = 'cancelled';    // Annulé

    /**
     * Label lisible pour l'affichage
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::SUBMITTED => 'Soumis',
            self::UNDER_ADMIN_REVIEW => 'En révision admin',
            self::ADMIN_REJECTED => 'Rejeté par admin',
            self::ADMIN_VALIDATED => 'Validé par admin',
            self::AVAILABLE_FOR_IMF => 'Disponible pour IMF',
            self::UNDER_INSTITUTION_REVIEW => 'En analyse institution',
            self::INTERVIEW_SCHEDULED => 'Entretien planifié',
            self::INTERVIEW_CONFIRMED => 'Entretien confirmé',
            self::DOCUMENTS_REQUESTED => 'Documents demandés',
            self::INSTITUTION_REJECTED => 'Rejeté par institution',
            self::INSTITUTION_ACCEPTED => 'Accepté par institution',
            self::FINANCING_PENDING => 'Financement en attente',
            self::FUNDED => 'Financé',
            self::ACTIVE => 'En cours',
            self::REPAID => 'Remboursé',
            self::CLOSED => 'Clôturé',
            self::SUSPENDED => 'Suspendu',
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé',
        };
    }

    /**
     * Couleur CSS pour le badge de statut
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::SUBMITTED => 'info',
            self::UNDER_ADMIN_REVIEW => 'warning',
            self::ADMIN_REJECTED => 'danger',
            self::ADMIN_VALIDATED => 'success',
            self::AVAILABLE_FOR_IMF => 'info',
            self::UNDER_INSTITUTION_REVIEW => 'warning',
            self::INTERVIEW_SCHEDULED => 'info',
            self::INTERVIEW_CONFIRMED => 'success',
            self::DOCUMENTS_REQUESTED => 'warning',
            self::INSTITUTION_REJECTED => 'danger',
            self::INSTITUTION_ACCEPTED => 'success',
            self::FINANCING_PENDING => 'warning',
            self::FUNDED => 'success',
            self::ACTIVE => 'primary',
            self::REPAID => 'success',
            self::CLOSED => 'dark',
            self::SUSPENDED => 'danger',
            self::COMPLETED => 'success',
            self::CANCELLED => 'dark',
        };
    }

    /**
     * Transitions autorisées depuis cet état
     * Retourne un tableau des états possibles suivants
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::SUBMITTED, self::CANCELLED],
            self::SUBMITTED => [self::UNDER_ADMIN_REVIEW, self::ADMIN_VALIDATED, self::ADMIN_REJECTED, self::CANCELLED, self::SUSPENDED],
            self::UNDER_ADMIN_REVIEW => [self::ADMIN_VALIDATED, self::ADMIN_REJECTED, self::CANCELLED, self::SUSPENDED],
            self::ADMIN_REJECTED => [self::SUBMITTED, self::CANCELLED],
            self::ADMIN_VALIDATED => [self::AVAILABLE_FOR_IMF, self::UNDER_INSTITUTION_REVIEW, self::CANCELLED, self::SUSPENDED],
            self::AVAILABLE_FOR_IMF => [self::UNDER_INSTITUTION_REVIEW, self::CANCELLED, self::SUSPENDED],
            self::UNDER_INSTITUTION_REVIEW => [
                self::INTERVIEW_SCHEDULED,
                self::DOCUMENTS_REQUESTED,
                self::INSTITUTION_REJECTED,
                self::INSTITUTION_ACCEPTED,
                self::CANCELLED,
                self::SUSPENDED,
            ],
            self::INTERVIEW_SCHEDULED => [self::INTERVIEW_CONFIRMED, self::INSTITUTION_REJECTED, self::CANCELLED, self::SUSPENDED],
            self::INTERVIEW_CONFIRMED => [self::INSTITUTION_ACCEPTED, self::INSTITUTION_REJECTED, self::CANCELLED, self::SUSPENDED],
            self::DOCUMENTS_REQUESTED => [self::UNDER_INSTITUTION_REVIEW, self::INSTITUTION_REJECTED, self::CANCELLED, self::SUSPENDED],
            self::INSTITUTION_REJECTED => [], // État final
            self::INSTITUTION_ACCEPTED => [self::FINANCING_PENDING, self::FUNDED, self::CANCELLED, self::SUSPENDED],
            self::FINANCING_PENDING => [self::FUNDED, self::CANCELLED, self::SUSPENDED],
            self::FUNDED => [self::ACTIVE, self::CANCELLED, self::SUSPENDED],
            self::ACTIVE => [self::REPAID, self::CANCELLED, self::SUSPENDED],
            self::REPAID => [self::CLOSED, self::CANCELLED, self::SUSPENDED],
            self::CLOSED => [self::AVAILABLE_FOR_IMF],
            self::SUSPENDED => [self::SUBMITTED, self::UNDER_ADMIN_REVIEW, self::ADMIN_VALIDATED, self::UNDER_INSTITUTION_REVIEW, self::ACTIVE, self::CANCELLED],
            self::COMPLETED => [], // État final
            self::CANCELLED => [], // État final
        };
    }

    /**
     * Vérifie si la transition vers l'état cible est autorisée
     */
    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Vérifie si c'est un état final (terminal)
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::INSTITUTION_REJECTED,
            self::COMPLETED,
            self::REPAID,
            self::CLOSED,
            self::CANCELLED,
        ], true);
    }

    /**
     * Vérifie si le projet est en cours (actif)
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::SUBMITTED,
            self::UNDER_ADMIN_REVIEW,
            self::ADMIN_VALIDATED,
            self::AVAILABLE_FOR_IMF,
            self::UNDER_INSTITUTION_REVIEW,
            self::INTERVIEW_SCHEDULED,
            self::INTERVIEW_CONFIRMED,
            self::DOCUMENTS_REQUESTED,
            self::INSTITUTION_ACCEPTED,
            self::FINANCING_PENDING,
            self::FUNDED,
            self::ACTIVE,
        ], true);
    }

    public function badgeClass(): string
    {
        return 'status-'.$this->color().' '.$this->color();
    }

    public function canOwnerModify(): bool
    {
        return in_array($this, [
            self::DRAFT,
            self::SUBMITTED,
            self::ADMIN_REJECTED,
        ], true);
    }

    public function canOwnerSubmit(): bool
    {
        return in_array($this, [
            self::DRAFT,
            self::ADMIN_REJECTED,
        ], true);
    }

    public function isFinanced(): bool
    {
        return in_array($this, [
            self::FUNDED,
            self::ACTIVE,
            self::REPAID,
            self::CLOSED,
            self::COMPLETED,
        ], true);
    }

    public static function fromStorage(self|string|null $status): ?self
    {
        if ($status instanceof self) {
            return $status;
        }

        if (! is_string($status) || $status === '') {
            return null;
        }

        return self::tryFrom($status);
    }

    public static function fromStorageOr(self|string|null $status, self $fallback = self::DRAFT): self
    {
        return self::fromStorage($status) ?? $fallback;
    }

    public static function institutionAvailableValues(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            [
                self::ADMIN_VALIDATED,
                self::AVAILABLE_FOR_IMF,
                self::UNDER_INSTITUTION_REVIEW,
                self::INTERVIEW_SCHEDULED,
                self::INTERVIEW_CONFIRMED,
                self::DOCUMENTS_REQUESTED,
                self::INSTITUTION_ACCEPTED,
            ]
        );
    }

    public static function institutionPortfolioValues(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            [
                self::FUNDED,
                self::ACTIVE,
                self::REPAID,
                self::CLOSED,
                self::COMPLETED,
            ]
        );
    }

    public static function institutionVisibleValues(): array
    {
        return array_values(array_unique([
            ...self::institutionAvailableValues(),
            ...self::institutionPortfolioValues(),
        ]));
    }

    public static function peutEtreSoumisNouveauFinancement(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            [
                self::CLOSED,
                self::COMPLETED,
                self::REPAID,
                self::CANCELLED,
            ]
        );
    }
}
