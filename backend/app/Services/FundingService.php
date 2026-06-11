<?php

namespace App\Services;

use App\Enums\FundingStatus;
use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\ProjectDocument;
use App\Models\ProjectStatusHistory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class FundingService
{
    public function getPorteurFundings(User $user): Collection
    {
        $projectIds = $user->projects()->pluck('id');

        return Funding::with(['project', 'institution'])
            ->whereIn('project_id', $projectIds)
            ->orderBy('date_financement', 'desc')
            ->get();
    }

    public function getPorteurFundingStats(User $user): array
    {
        $projects = $user->projects()->get(['id', 'montant_demande', 'montant_finance']);
        $projectIds = $projects->pluck('id');

        $montantDemande = (float) $projects->sum('montant_demande');
        $montantFinance = (float) $projects->sum('montant_finance');
        $montantRestant = max(0, $montantDemande - $montantFinance);
        $progression = $montantDemande > 0 ? round(($montantFinance / $montantDemande) * 100, 1) : 0;

        $institutionsCount = Funding::whereIn('project_id', $projectIds)
            ->distinct('institution_id')
            ->count('institution_id');

        $fundingsCount = Funding::whereIn('project_id', $projectIds)->count();
        $fundedProjectsCount = $projects->where('montant_finance', '>', 0)->count();
        $totalProjectsCount = $projects->count();

        $projectsWithFunding = $projects->filter(fn ($p) => (float) $p->montant_finance > 0)->count();

        $overfunded = $projects->filter(fn ($p) => (float) $p->montant_finance > (float) $p->montant_demande)->count();

        return [
            'montant_demande' => $montantDemande,
            'montant_finance' => $montantFinance,
            'montant_restant' => $montantRestant,
            'progression' => $progression,
            'institutions_count' => $institutionsCount,
            'fundings_count' => $fundingsCount,
            'projets_finances' => $fundedProjectsCount,
            'total_projets' => $totalProjectsCount,
            'projets_avec_financement' => $projectsWithFunding,
            'surfinancements' => $overfunded,
        ];
    }

    public function getPorteurFundingHistory(User $user): Collection
    {
        $projectIds = $user->projects()->pluck('id');

        $statusChanges = ProjectStatusHistory::with(['project', 'actor'])
            ->whereIn('project_id', $projectIds)
            ->whereIn('new_status', [
                ProjectStatus::INSTITUTION_ACCEPTED->value,
                ProjectStatus::FINANCING_PENDING->value,
                ProjectStatus::FUNDED->value,
                ProjectStatus::ACTIVE->value,
                ProjectStatus::REPAID->value,
                ProjectStatus::CLOSED->value,
                ProjectStatus::COMPLETED->value,
            ])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($h) {
                return [
                    'type' => 'statut',
                    'project_id' => $h->project_id,
                    'project_titre' => $h->project->titre ?? '',
                    'ancien_statut' => $h->old_status,
                    'nouveau_statut' => $h->new_status,
                    'raison' => $h->reason,
                    'acteur' => $h->actor->name ?? 'Système',
                    'date' => $h->created_at->format('d M Y H:i'),
                    'timestamp' => $h->created_at->timestamp,
                ];
            });

        $transactions = Transaction::with(['project'])
            ->whereIn('project_id', $projectIds)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($t) {
                return [
                    'type' => 'transaction',
                    'project_id' => $t->project_id,
                    'project_titre' => $t->project->titre ?? '',
                    'montant' => (float) $t->amount,
                    'methode' => $t->fedapay_payment_method ?? '',
                    'reference' => $t->fedapay_transaction_id ?? '',
                    'date' => ($t->paid_at ?? $t->created_at)->format('d M Y H:i'),
                    'timestamp' => ($t->paid_at ?? $t->created_at)->timestamp,
                ];
            });

        $merged = $statusChanges->concat($transactions)->sortByDesc('timestamp')->values();

        return $merged;
    }

    public function getFundingDocuments(User $user): Collection
    {
        $projectIds = $user->projects()->pluck('id');

        return ProjectDocument::with('project')
            ->whereIn('project_id', $projectIds)
            ->where(function ($q) {
                $q->where('type', 'contrat')
                    ->orWhere('type', 'convention')
                    ->orWhere('type', 'preuve')
                    ->orWhere('type', 'recu');
            })
            ->latest()
            ->get();
    }

    public function getFundingStatusLabels(): array
    {
        $labels = [];
        foreach (FundingStatus::cases() as $status) {
            $labels[$status->value] = [
                'label' => $status->label(),
                'color' => $status->color(),
            ];
        }

        return $labels;
    }

    public function getHistoryStatusMeta(string $newStatus): array
    {
        $meta = [
            'institution_accepted' => ['icon' => 'bi bi-check-circle', 'color' => 'info', 'label' => 'Accepté par institution'],
            'financing_pending' => ['icon' => 'bi bi-hourglass', 'color' => 'warning', 'label' => 'Financement en attente'],
            'funded' => ['icon' => 'bi bi-cash-stack', 'color' => 'success', 'label' => 'Financement confirmé'],
            'active' => ['icon' => 'bi bi-play-circle', 'color' => 'primary', 'label' => 'Projet activé'],
            'repaid' => ['icon' => 'bi bi-check-all', 'color' => 'success', 'label' => 'Projet remboursé'],
            'closed' => ['icon' => 'bi bi-lock', 'color' => 'dark', 'label' => 'Projet clôturé'],
            'completed' => ['icon' => 'bi bi-check-all', 'color' => 'success', 'label' => 'Projet terminé'],
        ];

        return $meta[$newStatus] ?? ['icon' => 'bi bi-arrow-right-circle', 'color' => 'secondary', 'label' => $newStatus];
    }
}
