<?php

namespace App\Enums;

enum EcheanceStatus: string
{
    case PENDING = 'pending';
    case UPCOMING = 'upcoming';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case PARTIAL = 'partial';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'À venir',
            self::UPCOMING => 'Prochaine',
            self::PAID => 'Payée',
            self::OVERDUE => 'En retard',
            self::PARTIAL => 'Partielle',
            self::FAILED => 'Échec',
            self::CANCELLED => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'secondary',
            self::UPCOMING => 'info',
            self::PAID => 'success',
            self::OVERDUE => 'danger',
            self::PARTIAL => 'warning',
            self::FAILED => 'danger',
            self::CANCELLED => 'danger',
        };
    }

    public function isPayee(): bool
    {
        return $this === self::PAID;
    }

    public function isEnRetard(): bool
    {
        return $this === self::OVERDUE;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::PAID, self::FAILED, self::CANCELLED]);
    }
}
