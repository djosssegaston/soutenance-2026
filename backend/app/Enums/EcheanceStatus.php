<?php

namespace App\Enums;

enum EcheanceStatus: string
{
    case PENDING = 'pending';
    case UPCOMING = 'upcoming';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case PARTIAL = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'À venir',
            self::UPCOMING => 'Prochaine',
            self::PAID => 'Payée',
            self::OVERDUE => 'En retard',
            self::PARTIAL => 'Partielle',
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
}
