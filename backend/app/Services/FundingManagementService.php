<?php

namespace App\Services;

use App\Enums\FundingStatus;
use App\Models\FinancementHistory;
use App\Models\Funding;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class FundingManagementService
{
    /**
     * Calcule la progression du financement pour un projet
     */
    public function getProjectFundingProgress(Project $project): float
    {
        $totalFunded = Funding::where('project_id', $project->id)
            ->whereIn('statut', [
                FundingStatus::APPROVED->value,
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
                FundingStatus::COMPLETED->value,
            ])
            ->sum('montant_valide');

        if ($project->montant_demande <= 0) {
            return 0;
        }

        return round(($totalFunded / $project->montant_demande) * 100, 2);
    }

    /**
     * Vérifie le risque de surfinancement
     */
    public function checkOverfunding(int $projectId, float $proposedAmount): bool
    {
        $project = Project::find($projectId);
        $currentTotal = Funding::where('project_id', $projectId)
            ->whereIn('statut', [
                FundingStatus::PROPOSED->value,
                FundingStatus::AWAITING_BORROWER_PLAN->value,
                FundingStatus::AWAITING_IMF_VALIDATION->value,
                FundingStatus::APPROVED->value,
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
            ])
            ->sum('montant_propose');

        return ($currentTotal + $proposedAmount) > ($project->montant_demande * 1.1); // Marge de 10%
    }

    /**
     * Enregistre une action dans l'historique
     */
    public function logAction(int $fundingId, string $action, string $author, string $details = '')
    {
        try {
            return FinancementHistory::create([
                'financement_id' => $fundingId,
                'action' => $action,
                'auteur' => $author,
                'details' => $details,
            ]);
        } catch (\Exception $e) {
            Log::warning('Erreur historisation financement', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
