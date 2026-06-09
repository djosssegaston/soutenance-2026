<?php

namespace App\Enums;

enum FundingStatus: string
{
    case PENDING = 'pending';
    case PROPOSED = 'proposed';
    case AWAITING_BORROWER_PLAN = 'awaiting_borrower_plan';
    case AWAITING_IMF_VALIDATION = 'awaiting_imf_validation';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DISBURSED = 'disbursed';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case DEFAULTED = 'defaulted';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PROPOSED => 'Proposition faite',
            self::AWAITING_BORROWER_PLAN => 'Attente plan porteur',
            self::AWAITING_IMF_VALIDATION => 'Attente validation IMF',
            self::APPROVED => 'Approuvé',
            self::REJECTED => 'Rejeté',
            self::DISBURSED => 'Décaissé',
            self::ACTIVE => 'Actif',
            self::COMPLETED => 'Terminé',
            self::DEFAULTED => 'En défaut',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'secondary',
            self::PROPOSED => 'info',
            self::AWAITING_BORROWER_PLAN => 'warning',
            self::AWAITING_IMF_VALIDATION => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::DISBURSED => 'primary',
            self::ACTIVE => 'primary',
            self::COMPLETED => 'success',
            self::DEFAULTED => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::DISBURSED,
            self::ACTIVE,
        ]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::DEFAULTED,
            self::REJECTED,
        ]);
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::PROPOSED, self::REJECTED],
            self::PROPOSED => [self::AWAITING_BORROWER_PLAN, self::REJECTED],
            self::AWAITING_BORROWER_PLAN => [self::AWAITING_IMF_VALIDATION, self::REJECTED],
            self::AWAITING_IMF_VALIDATION => [self::APPROVED, self::REJECTED, self::AWAITING_BORROWER_PLAN],
            self::APPROVED => [self::DISBURSED, self::REJECTED],
            self::DISBURSED => [self::ACTIVE, self::DEFAULTED],
            self::ACTIVE => [self::COMPLETED, self::DEFAULTED],
            self::COMPLETED => [],
            self::DEFAULTED => [],
            self::REJECTED => [],
        };
    }
}
