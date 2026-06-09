<?php

namespace App\Services;

use App\Models\Repayment;
use App\Models\RepaymentEvent;

class RepaymentMonitoringService
{
    /**
     * Calcule le niveau de risque d'un remboursement
     */
    public function calculateRiskLevel(Repayment $repayment): string
    {
        $dueDate = $repayment->date_echeance;
        $now = now();

        if ($repayment->statut === 'paye') {
            return 'faible';
        }

        if ($now->greaterThan($dueDate)) {
            $daysLate = $now->diffInDays($dueDate);
            if ($daysLate > 90) {
                return 'critique';
            }
            if ($daysLate > 30) {
                return 'eleve';
            }

            return 'moyen';
        }

        return 'faible';
    }

    /**
     * Enregistre un événement de remboursement
     */
    public function logEvent(int $repaymentId, string $type, string $details, string $author)
    {
        return RepaymentEvent::create([
            'repayment_id' => $repaymentId,
            'type_evenement' => $type,
            'details' => $details,
            'auteur' => $author,
        ]);
    }

    /**
     * Calcule les pénalités basées sur le retard (simulé)
     */
    public function calculatePenalties(Repayment $repayment): float
    {
        if ($repayment->statut === 'paye') {
            return 0;
        }

        $dueDate = $repayment->date_echeance;
        if (now()->lessThanOrEqualTo($dueDate)) {
            return 0;
        }

        $daysLate = now()->diffInDays($dueDate);

        // Exemple: 0.1% par jour de retard
        return round($repayment->montant_total * ($daysLate * 0.001), 2);
    }
}
