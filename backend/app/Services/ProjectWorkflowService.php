<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProjectWorkflowService
{
    /**
     * Effectue une transition de statut pour un projet
     *
     * @param  Project  $project  Le projet à modifier
     * @param  ProjectStatus  $newStatus  Le nouveau statut
     * @param  string|null  $reason  Raison de la transition (optionnel)
     * @param  int|null  $actor_id  ID de l'utilisateur qui effectue l'action
     *
     * @throws RuntimeException Si la transition n'est pas autorisée
     */
    public function transition(
        Project $project,
        ProjectStatus $newStatus,
        ?string $reason = null,
        ?int $actor_id = null
    ): ProjectStatusHistory {
        $currentStatus = ProjectStatus::tryFrom($project->statut);

        if (! $currentStatus) {
            throw new RuntimeException("Statut actuel invalide: {$project->statut}");
        }

        // Vérifier que la transition est autorisée
        if (! $currentStatus->canTransitionTo($newStatus)) {
            throw new RuntimeException(
                "Transition non autorisée: {$currentStatus->label()} → {$newStatus->label()}"
            );
        }

        // Exécuter dans une transaction pour garantir la cohérence
        return DB::transaction(function () use ($project, $currentStatus, $newStatus, $reason, $actor_id) {
            // Mettre à jour le statut du projet
            $project->statut = $newStatus->value;
            $project->save();

            // Créer l'entrée dans l'historique
            $history = ProjectStatusHistory::create([
                'project_id' => $project->id,
                'old_status' => $currentStatus->value,
                'new_status' => $newStatus->value,
                'reason' => $reason,
                'actor_id' => $actor_id,
                'metadata' => $this->buildMetadata($project, $currentStatus, $newStatus),
            ]);

            // Logger la transition
            Log::info('Project status transition', [
                'project_id' => $project->id,
                'old_status' => $currentStatus->value,
                'new_status' => $newStatus->value,
                'actor_id' => $actor_id,
                'reason' => $reason,
            ]);

            return $history;
        });
    }

    /**
     * Soumettre un projet (Draft → Submitted)
     */
    public function submit(Project $project, int $actor_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::SUBMITTED,
            'Projet soumis par le porteur',
            $actor_id
        );
    }

    /**
     * Admin valide un projet (Under Admin Review → Admin Validated)
     */
    public function adminValidate(Project $project, int $admin_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::ADMIN_VALIDATED,
            'Validé par l\'administrateur',
            $admin_id
        );
    }

    /**
     * Admin rejette un projet (Under Admin Review → Admin Rejected)
     */
    public function adminReject(Project $project, int $admin_id, string $reason): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::ADMIN_REJECTED,
            $reason,
            $admin_id
        );
    }

    /**
     * Rendre le projet disponible pour les IMF (Admin Validated → Available For IMF)
     */
    public function makeAvailableForIMF(Project $project, int $admin_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::AVAILABLE_FOR_IMF,
            'Projet disponible pour les institutions financières',
            $admin_id
        );
    }

    /**
     * Institution commence l'analyse (Admin Validated → Under Institution Review)
     */
    public function startInstitutionReview(Project $project, int $institution_user_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::UNDER_INSTITUTION_REVIEW,
            'En analyse par l\'institution',
            $institution_user_id
        );
    }

    /**
     * Planifier un entretien (Under Institution Review → Interview Scheduled)
     */
    public function scheduleInterview(Project $project, int $institution_user_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::INTERVIEW_SCHEDULED,
            'Entretien planifié',
            $institution_user_id
        );
    }

    /**
     * Confirmer un entretien (Interview Scheduled → Interview Confirmed)
     */
    public function confirmInterview(Project $project, int $institution_user_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::INTERVIEW_CONFIRMED,
            'Entretien confirmé',
            $institution_user_id
        );
    }

    /**
     * Demander des documents (Under Institution Review → Documents Requested)
     */
    public function requestDocuments(Project $project, int $institution_user_id, string $reason): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::DOCUMENTS_REQUESTED,
            $reason,
            $institution_user_id
        );
    }

    /**
     * Institution accepte un projet (Under Institution Review → Institution Accepted)
     */
    public function institutionAccept(Project $project, int $institution_user_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::INSTITUTION_ACCEPTED,
            'Accepté par l\'institution',
            $institution_user_id
        );
    }

    /**
     * Institution rejette un projet (Any institution state → Institution Rejected)
     */
    public function institutionReject(Project $project, int $institution_user_id, string $reason): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::INSTITUTION_REJECTED,
            $reason,
            $institution_user_id
        );
    }

    /**
     * Enregistrer le financement (Institution Accepted → Funded)
     */
    public function markAsFunded(Project $project, int $actor_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::FUNDED,
            'Financement enregistré',
            $actor_id
        );
    }

    /**
     * Activer le projet (Funded → Active)
     */
    public function activate(Project $project, int $actor_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::ACTIVE,
            'Projet activé - Remboursements en cours',
            $actor_id
        );
    }

    /**
     * Marquer comme complété (Active → Completed)
     */
    public function complete(Project $project, int $actor_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::COMPLETED,
            'Projet complété',
            $actor_id
        );
    }

    /**
     * Marquer comme remboursé (Active → Repaid)
     */
    public function repay(Project $project, int $actor_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::REPAID,
            'Tous les remboursements effectués',
            $actor_id
        );
    }

    /**
     * Clôturer le projet (Repaid → Closed)
     */
    public function close(Project $project, int $actor_id): ProjectStatusHistory
    {
        return $this->transition(
            $project,
            ProjectStatus::CLOSED,
            'Projet clôturé - prêt pour un nouveau financement',
            $actor_id
        );
    }

    /**
     * Suspendre un projet (Any non-terminal state → Suspended)
     */
    public function suspend(Project $project, int $actor_id, string $reason): ProjectStatusHistory
    {
        $currentStatus = ProjectStatus::tryFrom($project->statut);

        if ($currentStatus && $currentStatus->isTerminal()) {
            throw new RuntimeException("Impossible de suspendre un projet dans un état terminal: {$currentStatus->label()}");
        }

        return $this->transition(
            $project,
            ProjectStatus::SUSPENDED,
            $reason,
            $actor_id
        );
    }

    /**
     * Annuler un projet (Any non-terminal state → Cancelled)
     */
    public function cancel(Project $project, int $actor_id, string $reason): ProjectStatusHistory
    {
        $currentStatus = ProjectStatus::tryFrom($project->statut);

        if ($currentStatus && $currentStatus->isTerminal()) {
            throw new RuntimeException("Impossible d'annuler un projet dans un état terminal: {$currentStatus->label()}");
        }

        return $this->transition(
            $project,
            ProjectStatus::CANCELLED,
            $reason,
            $actor_id
        );
    }

    /**
     * Obtenir l'historique complet des statuts d'un projet
     */
    public function getHistory(Project $project): array
    {
        return $project->statusHistories()
            ->with('actor:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($history) {
                return [
                    'old_status' => ProjectStatus::tryFrom($history->old_status)?->label() ?? $history->old_status,
                    'new_status' => ProjectStatus::tryFrom($history->new_status)?->label() ?? $history->new_status,
                    'reason' => $history->reason,
                    'actor' => $history->actor?->name ?? 'Système',
                    'date' => $history->created_at->format('d/m/Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Vérifier si un projet peut être transitions vers un statut
     */
    public function canTransition(Project $project, ProjectStatus $targetStatus): bool
    {
        $currentStatus = ProjectStatus::tryFrom($project->statut);

        if (! $currentStatus) {
            return false;
        }

        return $currentStatus->canTransitionTo($targetStatus);
    }

    /**
     * Obtenir les transitions possibles pour un projet
     */
    public function getPossibleTransitions(Project $project): array
    {
        $currentStatus = ProjectStatus::tryFrom($project->statut);

        if (! $currentStatus) {
            return [];
        }

        return array_map(
            fn ($status) => ['value' => $status->value, 'label' => $status->label()],
            $currentStatus->allowedTransitions()
        );
    }

    /**
     * Construire les métadonnées pour l'historique
     */
    private function buildMetadata(
        Project $project,
        ProjectStatus $oldStatus,
        ProjectStatus $newStatus
    ): array {
        return [
            'project_title' => $project->titre,
            'project_sector' => $project->secteur,
            'amount' => $project->montant_demande,
            'transition_timestamp' => now()->toIso8601String(),
        ];
    }
}
