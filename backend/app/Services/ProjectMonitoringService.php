<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\Repayment;
use Illuminate\Support\Facades\DB;

class ProjectMonitoringService
{
    /**
     * Calcule le score de risque d'un projet
     */
    public function calculateRiskScore(Project $project): array
    {
        $score = 0;
        $analysis = [];

        // 1. Ratio financement/demande
        $ratio = $project->montant_demande > 0
            ? ($project->montant_finance / $project->montant_demande) * 100
            : 0;

        if ($ratio > 100) {
            $score += 30;
            $analysis[] = 'Surfinancement détecté : '.round($ratio, 2).'%';
        }

        // 2. Retards de remboursement
        $lateRepayments = Repayment::where('project_id', $project->id)
            ->where('statut', 'en_retard')
            ->count();

        if ($lateRepayments > 0) {
            $score += $lateRepayments * 20;
            $analysis[] = "$lateRepayments retards de remboursement constatés.";
        }

        // 3. Litiges ouverts
        $disputesCount = DB::table('disputes')
            ->where('project_id', $project->id)
            ->where('statut', 'ouvert')
            ->count();

        if ($disputesCount > 0) {
            $score += 50;
            $analysis[] = 'Litige actif sur ce projet.';
        }

        // Normalisation
        $score = min(100, $score);

        $level = 'faible';
        if ($score >= 80) {
            $level = 'critique';
        } elseif ($score >= 50) {
            $level = 'élevé';
        } elseif ($score >= 25) {
            $level = 'moyen';
        }

        return [
            'score' => $score,
            'level' => $level,
            'analysis' => implode(' | ', $analysis) ?: 'Aucune anomalie détectée.',
        ];
    }

    /**
     * Récupère les statistiques globales pour le dashboard admin
     */
    public function getGlobalStats(): array
    {
        return [
            'projects' => [
                'total' => Project::count(),
                'active' => Project::where('statut', ProjectStatus::ACTIVE->value)->count(),
                'suspended' => Project::where('statut', ProjectStatus::SUSPENDED->value)->count(),
                'completed' => Project::where('statut', ProjectStatus::COMPLETED->value)->count(),
            ],
            'validation' => [
                'pending' => Project::whereIn('statut', [ProjectStatus::SUBMITTED->value, ProjectStatus::UNDER_ADMIN_REVIEW->value])->count(),
                'validated' => Project::where('statut', ProjectStatus::ADMIN_VALIDATED->value)->count(),
                'rejected' => Project::where('statut', ProjectStatus::ADMIN_REJECTED->value)->count(),
                'draft' => Project::where('statut', ProjectStatus::DRAFT->value)->count(),
            ],
            'finance' => [
                'total_funded' => Project::sum('montant_finance'),
                'total_repaid' => Repayment::where('statut', 'paye')->sum('montant_rembourse'),
                'high_risk_count' => DB::table('project_risk_scores')->whereIn('risk_level', ['élevé', 'critique'])->count(),
            ],
        ];
    }

    /**
     * Récupère les alertes critiques
     */
    public function getCriticalAlerts(): array
    {
        $alerts = [];

        // Surfinancements
        $overfunded = Project::whereColumn('montant_finance', '>', 'montant_demande')->get();
        foreach ($overfunded as $p) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Surfinancement critique',
                'message' => "Le projet #{$p->id} a dépassé son budget de demande.",
                'project_id' => $p->id,
                'date' => $p->updated_at,
            ];
        }

        // Retards
        $late = Repayment::with('project')->where('statut', 'en_retard')->get();
        foreach ($late as $r) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Remboursement en retard',
                'message' => "Échéance dépassée pour le projet #{$r->project_id}.",
                'project_id' => $r->project_id,
                'date' => $r->date_echeance,
            ];
        }

        return $alerts;
    }
}
