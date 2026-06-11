<?php

namespace App\Services;

use App\Enums\EcheanceStatus;
use App\Enums\FundingStatus;
use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\Project;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FinancingWorkflowService
{
    public function __construct(
        protected ProjectWorkflowService $workflow,
        protected EcheanceService $echeanceService,
        protected FedaPayService $fedaPayService,
    ) {}

    public function imfFaireProposition(User $user, Project $project, array $data): Funding
    {
        $institution = $user->institution;
        if (! $institution) {
            throw new RuntimeException('Institution introuvable.');
        }

        if (! $project->peutEtreFinance()) {
            $statusLabel = $project->statusEnum()->label();

            throw new RuntimeException("Ce projet ne peut pas recevoir de proposition de financement. Statut actuel : {$statusLabel}.");
        }

        $existing = Funding::where('project_id', $project->id)
            ->where('institution_id', $institution->id)
            ->whereIn('statut', [
                FundingStatus::PROPOSED->value,
                FundingStatus::AWAITING_BORROWER_PLAN->value,
                FundingStatus::AWAITING_IMF_VALIDATION->value,
                FundingStatus::APPROVED->value,
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
            ])
            ->exists();

        if ($existing) {
            throw new RuntimeException('Vous avez déjà une proposition de financement active pour ce projet.');
        }

        $statutProjet = $project->statusEnum();

        if ($statutProjet === ProjectStatus::ADMIN_VALIDATED) {
            $this->workflow->startInstitutionReview($project, $user->id);
            $statutProjet = $project->fresh()->statusEnum();
        }

        if (! in_array($statutProjet->value, [
            ProjectStatus::UNDER_INSTITUTION_REVIEW->value,
            ProjectStatus::INSTITUTION_ACCEPTED->value,
        ])) {
            if ($statutProjet->canTransitionTo(ProjectStatus::UNDER_INSTITUTION_REVIEW)) {
                $this->workflow->transition($project, ProjectStatus::UNDER_INSTITUTION_REVIEW, 'Début analyse par IMF', $user->id);
            } else {
                throw new RuntimeException('Le projet n\'est pas disponible pour une proposition de financement.');
            }
        }

        $funding = DB::transaction(function () use ($project, $institution, $user, $data) {
            if ($project->statut === ProjectStatus::UNDER_INSTITUTION_REVIEW->value) {
                $this->workflow->institutionAccept($project, $user->id);
            }

            $funding = Funding::create([
                'project_id' => $project->id,
                'institution_id' => $institution->id,
                'porteur_id' => $project->user_id,
                'montant' => $data['montant_propose'],
                'montant_demande' => $project->montant_demande,
                'montant_propose' => $data['montant_propose'],
                'taux_interet' => $data['taux_interet'],
                'duree' => $data['duree'],
                'statut' => FundingStatus::AWAITING_BORROWER_PLAN->value,
                'conditions' => $data['conditions'] ?? null,
                'frais' => $data['frais'] ?? null,
                'commentaires' => $data['commentaires'] ?? null,
                'date_financement' => now(),
            ]);

            $this->logAction($funding->id, 'proposition_imf', $user->name,
                "Proposition de {$data['montant_propose']} FCFA à {$data['taux_interet']}% sur {$data['duree']} mois"
            );

            return $funding;
        });

        $this->notifierPorteur(
            $project->user_id,
            'Nouvelle proposition de financement',
            "L'institution {$institution->nom} propose un financement de ".
            number_format((float) $data['montant_propose'], 0, ',', ' ').
            " FCFA pour votre projet \"{$project->titre}\". Veuillez définir votre plan de remboursement.",
            'financement_proposition'
        );

        return $funding;
    }

    public function porteurSoumettrePlan(User $user, Funding $funding, array $data): Funding
    {
        if ($funding->project->user_id !== $user->id) {
            throw new RuntimeException('Ce financement ne vous appartient pas.');
        }

        if ($funding->statut !== FundingStatus::AWAITING_BORROWER_PLAN->value) {
            throw new RuntimeException('Ce financement n\'attend pas votre plan de remboursement.');
        }

        $montantMensuel = (float) ($data['montant_mensuel']);
        $jourRemboursement = (int) ($data['jour_remboursement']);

        if ($montantMensuel <= 0) {
            throw new RuntimeException('Le montant mensuel doit être supérieur à 0.');
        }

        if ($jourRemboursement < 1 || $jourRemboursement > 31) {
            throw new RuntimeException('Le jour de remboursement doit être entre 1 et 31.');
        }

        $funding->update([
            'montant_mensuel' => $montantMensuel,
            'jour_remboursement' => $jourRemboursement,
            'commentaire_plan' => $data['commentaire'] ?? null,
            'statut' => FundingStatus::AWAITING_IMF_VALIDATION->value,
            'date_acceptation_porteur' => now(),
            'validated_by_porteur_id' => $user->id,
        ]);

        $this->logAction($funding->id, 'plan_porteur', $user->name,
            "Porteur propose {$montantMensuel} FCFA/mois, jour {$jourRemboursement}"
        );

        $this->notifierInstitution(
            $funding->institution->user_id,
            'Plan de remboursement soumis',
            "Le porteur a soumis son plan de remboursement pour le projet \"{$funding->project->titre}\". ".
            'Montant mensuel proposé : '.number_format($montantMensuel, 0, ',', ' ').' FCFA.',
            'plan_remboursement_soumis'
        );

        return $funding;
    }

    public function imfApprouverPlan(User $user, Funding $funding, bool $skipDisbursement = false): Funding
    {
        $institution = $user->institution;
        if (! $institution || $funding->institution_id !== $institution->id) {
            throw new RuntimeException('Vous n\'êtes pas autorisé à approuver ce plan.');
        }

        if ($funding->statut !== FundingStatus::AWAITING_IMF_VALIDATION->value) {
            throw new RuntimeException('Ce plan n\'est pas en attente de validation IMF.');
        }

        DB::transaction(function () use ($funding, $user, $skipDisbursement) {
            $funding->update([
                'statut' => FundingStatus::APPROVED->value,
                'montant_valide' => $funding->montant_propose,
                'date_validation' => now(),
                'date_approbation_imf' => now(),
            ]);

            if (! $skipDisbursement) {
                $funding->update([
                    'montant_decaisse' => $funding->montant_propose,
                    'date_decaissement' => now(),
                ]);

                $project = $funding->project;
                $project->update([
                    'montant_finance' => (float) $project->montant_finance + (float) $funding->montant_propose,
                ]);

                $this->workflow->markAsFunded($project, $user->id);
            }

            $this->logAction($funding->id, 'approbation_imf', $user->name,
                'IMF approuve le plan et valide le financement'
            );
        });

        $funding = $funding->fresh();

        if ($skipDisbursement) {
            // Paiement IMF requis avant décaissement
            $this->notifierPorteur(
                $funding->project->user_id,
                'Plan de remboursement approuvé',
                "L'institution {$funding->institution->nom} a approuvé votre plan de remboursement pour le projet \"{$funding->project->titre}\". ".
                'Le décaissement sera effectué après validation du paiement.',
                'plan_approuve'
            );

            return $funding;
        }

        $echeances = $this->echeanceService->genererEcheancier($funding);

        if (! empty($echeances)) {
            $funding->update(['statut' => FundingStatus::DISBURSED->value]);

            $project = $funding->project;
            $this->workflow->activate($project, $user->id);

            // Initier le décaissement via FedaPay Payout
            try {
                $this->fedaPayService->initierDecaissement($funding->fresh());
            } catch (\Exception $e) {
                Log::warning('FedaPay disbursement initiation failed (non bloquant)', [
                    'financement_id' => $funding->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notifierPorteur(
            $funding->project->user_id,
            'Plan de remboursement approuvé',
            "L'institution {$funding->institution->nom} a approuvé votre plan de remboursement pour le projet \"{$funding->project->titre}\". ".
            'Le financement de '.number_format((float) $funding->montant_propose, 0, ',', ' ').' FCFA est décaissé.',
            'plan_approuve'
        );

        return $funding->fresh();
    }

    public function finaliserDecaissement(Funding $funding, User $user): Funding
    {
        if ($funding->statut !== FundingStatus::APPROVED->value) {
            throw new RuntimeException('Le financement doit être approuvé avant décaissement.');
        }

        DB::transaction(function () use ($funding, $user) {
            $funding->update([
                'montant_decaisse' => $funding->montant_propose,
                'date_decaissement' => now(),
            ]);

            $project = $funding->project;
            $project->update([
                'montant_finance' => (float) $project->montant_finance + (float) $funding->montant_propose,
            ]);

            $this->workflow->markAsFunded($project, $user->id);

            $this->logAction($funding->id, 'decaissement_imf', $user->name,
                'Paiement IMF confirmé, décaissement effectué'
            );
        });

        $funding = $funding->fresh();

        $echeances = $this->echeanceService->genererEcheancier($funding);

        if (! empty($echeances)) {
            $funding->update(['statut' => FundingStatus::DISBURSED->value]);

            $project = $funding->project;
            $this->workflow->activate($project, $user->id);

            try {
                $this->fedaPayService->initierDecaissement($funding->fresh());
            } catch (\Exception $e) {
                Log::warning('FedaPay disbursement initiation failed (non bloquant)', [
                    'financement_id' => $funding->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notifierPorteur(
            $funding->project->user_id,
            'Financement décaissé',
            'Le financement de '.number_format((float) $funding->montant_propose, 0, ',', ' ').
            " FCFA pour le projet \"{$funding->project->titre}\" a été décaissé. Les remboursements commencent.",
            'financement_decaissement'
        );

        return $funding->fresh();
    }

    public function imfRejeterPlan(User $user, Funding $funding, string $motif): Funding
    {
        $institution = $user->institution;
        if (! $institution || $funding->institution_id !== $institution->id) {
            throw new RuntimeException('Vous n\'êtes pas autorisé à rejeter ce plan.');
        }

        if ($funding->statut !== FundingStatus::AWAITING_IMF_VALIDATION->value) {
            throw new RuntimeException('Ce plan n\'est pas en attente de validation IMF.');
        }

        $funding->update([
            'statut' => FundingStatus::REJECTED->value,
            'date_rejet_imf' => now(),
            'motif_rejet_imf' => $motif,
        ]);

        $this->logAction($funding->id, 'rejet_imf', $user->name,
            "IMF rejette le plan : {$motif}"
        );

        $this->notifierPorteur(
            $funding->project->user_id,
            'Plan de remboursement rejeté',
            "L'institution {$funding->institution->nom} a rejeté votre plan de remboursement pour le projet \"{$funding->project->titre}\". Motif : {$motif}",
            'plan_rejete'
        );

        return $funding;
    }

    public function imfDemanderRevision(User $user, Funding $funding, string $commentaire): Funding
    {
        $institution = $user->institution;
        if (! $institution || $funding->institution_id !== $institution->id) {
            throw new RuntimeException('Vous n\'êtes pas autorisé à demander une révision.');
        }

        if ($funding->statut !== FundingStatus::AWAITING_IMF_VALIDATION->value) {
            throw new RuntimeException('Ce plan n\'est pas en attente de validation IMF.');
        }

        $funding->update([
            'statut' => FundingStatus::AWAITING_BORROWER_PLAN->value,
            'commentaire_plan' => $commentaire,
        ]);

        $this->logAction($funding->id, 'revision_demandee', $user->name,
            "IMF demande révision : {$commentaire}"
        );

        $this->notifierPorteur(
            $funding->project->user_id,
            'Révision du plan demandée',
            "L'institution {$funding->institution->nom} demande une révision de votre plan de remboursement. Commentaire : {$commentaire}",
            'revision_demandee'
        );

        return $funding;
    }

    public function cloturer(Funding $funding): Funding
    {
        if ($funding->statut !== FundingStatus::COMPLETED->value) {
            $funding->update([
                'statut' => FundingStatus::COMPLETED->value,
                'date_cloture' => now(),
            ]);
        }

        $project = $funding->project;
        $actorId = $funding->institution->user_id ?? 1;

        if ($project && $project->statut === ProjectStatus::ACTIVE->value) {
            $this->workflow->repay($project, $actorId);
        }

        if ($project && $project->fresh()->statut === ProjectStatus::REPAID->value) {
            $this->workflow->close($project, $actorId);
        }

        $this->notifierPorteur(
            $funding->project->user_id,
            'Financement clôturé',
            'Le financement de '.number_format((float) $funding->montant_propose, 0, ',', ' ').
            " FCFA pour le projet \"{$funding->project->titre}\" est clôturé. Un nouveau financement est désormais possible.",
            'financement_cloture'
        );

        return $funding;
    }

    public function verifierEtCloturer(Funding $funding): bool
    {
        $totalEcheances = $funding->echeances()->count();
        $payees = $funding->echeances()->where('statut', EcheanceStatus::PAID->value)->count();

        if ($totalEcheances > 0 && $payees >= $totalEcheances) {
            $this->cloturer($funding);

            return true;
        }

        return false;
    }

    protected function logAction(int $fundingId, string $action, string $author, string $details = '')
    {
        try {
            \App\Models\FinancementHistory::create([
                'financement_id' => $fundingId,
                'action' => $action,
                'auteur' => $author,
                'details' => $details,
            ]);
        } catch (\Exception $e) {
            Log::warning('Erreur historisation financement', ['error' => $e->getMessage()]);
        }
    }

    protected function notifierPorteur(int $userId, string $title, string $content, string $type): void
    {
        try {
            UserNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::warning('Erreur notification porteur', ['error' => $e->getMessage()]);
        }
    }

    protected function notifierInstitution(int $userId, string $title, string $content, string $type): void
    {
        try {
            UserNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::warning('Erreur notification institution', ['error' => $e->getMessage()]);
        }
    }
}
