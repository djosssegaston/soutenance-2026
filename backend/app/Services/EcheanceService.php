<?php

namespace App\Services;

use App\Enums\EcheanceStatus;
use App\Enums\FundingStatus;
use App\Enums\ProjectStatus;
use App\Models\Echeance;
use App\Models\Funding;
use App\Models\Repayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EcheanceService
{
    public function genererEcheancier(Funding $funding): array
    {
        $capital = (float) ($funding->montant_valide ?: $funding->montant_propose);
        $tauxAnnuel = (float) $funding->taux_interet;
        $montantMensuel = (float) ($funding->montant_mensuel ?: 0);
        $jourRemboursement = (int) ($funding->jour_remboursement ?: 1);
        $dateDecaissement = $funding->date_decaissement
            ? Carbon::parse($funding->date_decaissement)
            : now();

        if ($montantMensuel <= 0) {
            throw new \InvalidArgumentException('Le montant mensuel doit être supérieur à 0.');
        }

        // Taux mensuel fixe (taux annuel / 12)
        $tauxMensuel = ($tauxAnnuel / 100) / 12;

        $capitalRestant = $capital;
        $echeances = [];
        $totalInterets = 0;

        DB::beginTransaction();
        try {
            $funding->echeances()->delete();
            Repayment::where('financement_id', $funding->id)->delete();

            $i = 1;
            while (round($capitalRestant, 2) > 0) {
                $dateEcheance = $this->calculerDateEcheance($dateDecaissement, $i, $jourRemboursement);

                // Intérêts sur le capital restant au taux mensuel
                $interetsEcheance = round($capitalRestant * $tauxMensuel, 2);

                // Le total (capital + intérêts) ne doit pas dépasser le montant mensuel
                // Capital = montant mensuel - intérêts
                $capitalTheorique = round($montantMensuel - $interetsEcheance, 2);

                // Dernière échéance : on prend tout le capital restant
                if ($capitalTheorique >= $capitalRestant) {
                    $capitalEcheance = round($capitalRestant, 2);
                    $montantEcheance = round($capitalEcheance + $interetsEcheance, 2);
                } else {
                    $capitalEcheance = max(0, $capitalTheorique);
                    $montantEcheance = $montantMensuel;
                }

                $capitalRestant = round($capitalRestant - $capitalEcheance, 2);
                $totalInterets += $interetsEcheance;

                $statut = EcheanceStatus::PENDING->value;
                if ($dateEcheance->isPast() && $dateEcheance->isBefore(now()->subDay())) {
                    $statut = EcheanceStatus::OVERDUE->value;
                }

                $echeance = Echeance::create([
                    'financement_id' => $funding->id,
                    'project_id' => $funding->project_id,
                    'institution_id' => $funding->institution_id,
                    'numero_echeance' => $i,
                    'montant_capital' => $capitalEcheance,
                    'montant_interets' => $interetsEcheance,
                    'montant_frais' => 0,
                    'montant_total' => $montantEcheance,
                    'montant_paye' => 0,
                    'montant_restant' => $montantEcheance,
                    'date_echeance' => $dateEcheance,
                    'statut' => $statut,
                    'penalites' => 0,
                ]);

                $echeances[] = $echeance;

                $repaymentStatus = match ($statut) {
                    'paid' => 'paye',
                    'overdue' => 'en_retard',
                    'partial' => 'partiel',
                    default => 'en_attente',
                };

                $riskLevel = match ($statut) {
                    'overdue' => 'eleve',
                    'partial' => 'moyen',
                    default => 'faible',
                };

                Repayment::updateOrCreate(
                    [
                        'financement_id' => $funding->id,
                        'date_echeance' => $dateEcheance,
                    ],
                    [
                        'project_id' => $funding->project_id,
                        'institution_id' => $funding->institution_id,
                        'montant_total' => $montantEcheance,
                        'montant_rembourse' => 0,
                        'montant_restant' => $montantEcheance,
                        'statut' => $repaymentStatus,
                        'niveau_risque' => $riskLevel,
                        'penalites' => 0,
                    ]
                );

                $i++;
            }

            $funding->update([
                'echeances_generees' => count($echeances),
            ]);

            DB::commit();

            Log::info('Échéancier généré', [
                'financement_id' => $funding->id,
                'nombre_echeances' => count($echeances),
                'capital' => $capital,
                'taux_mensuel' => $tauxMensuel,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur génération échéancier', [
                'financement_id' => $funding->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $echeances;
    }

    public function calculerDateEcheance(Carbon $dateBase, int $numeroEcheance, int $jour): Carbon
    {
        $date = $dateBase->copy()->addMonths($numeroEcheance);

        $maxJour = $date->daysInMonth;
        $jourEffectif = min($jour, $maxJour);

        $date->setDay($jourEffectif);

        return $date;
    }

    public function mettreAJourStatutsEcheances(Funding $funding): void
    {
        $now = now();
        $echeances = $funding->echeances()->whereNotIn('statut', [EcheanceStatus::PAID->value])->get();

        foreach ($echeances as $echeance) {
            $dateEcheance = Carbon::parse($echeance->date_echeance);

            if ($dateEcheance->isPast() && $dateEcheance->isBefore($now->copy()->subDay())) {
                $echeance->update(['statut' => EcheanceStatus::OVERDUE->value]);
                // Sync to Repayment
                Repayment::where('financement_id', $echeance->financement_id)
                    ->where('date_echeance', $echeance->date_echeance)
                    ->update(['statut' => 'en_retard', 'niveau_risque' => 'eleve']);
            } elseif ($dateEcheance->isFuture() && $dateEcheance->diffInDays($now) <= 7) {
                $echeance->update(['statut' => EcheanceStatus::UPCOMING->value]);
                // Sync to Repayment
                Repayment::where('financement_id', $echeance->financement_id)
                    ->where('date_echeance', $echeance->date_echeance)
                    ->update(['statut' => 'en_attente']);
            }
        }
    }

    public function enregistrerPaiement(Echeance $echeance, float $montant, string $methode = 'manuel', ?string $reference = null): Echeance
    {
        $nouveauPaye = round((float) $echeance->montant_paye + $montant, 2);
        $montantTotal = (float) $echeance->montant_total;

        $updateData = [
            'montant_paye' => $nouveauPaye,
            'montant_restant' => round(max(0, $montantTotal - $nouveauPaye), 2),
            'date_paiement' => now(),
            'methode_paiement' => $methode,
        ];

        if ($reference) {
            $updateData['transaction_reference'] = $reference;
        }

        if ($nouveauPaye >= $montantTotal) {
            $updateData['statut'] = EcheanceStatus::PAID->value;
            $updateData['montant_restant'] = 0;
            $updateData['montant_paye'] = $montantTotal;
        } elseif ($nouveauPaye > 0) {
            $updateData['statut'] = EcheanceStatus::PARTIAL->value;
        }

        $echeance->update($updateData);

        // Sync payment to the corresponding Repayment record
        $repaymentStatus = match ($updateData['statut'] ?? $echeance->statut) {
            'paid' => 'paye',
            'overdue' => 'en_retard',
            'partial' => 'partiel',
            default => 'en_attente',
        };

        Repayment::updateOrCreate(
            [
                'financement_id' => $echeance->financement_id,
                'date_echeance' => $echeance->date_echeance,
            ],
            [
                'project_id' => $echeance->project_id,
                'institution_id' => $echeance->institution_id,
                'montant_total' => $montantTotal,
                'montant_rembourse' => $nouveauPaye,
                'montant_restant' => round(max(0, $montantTotal - $nouveauPaye), 2),
                'statut' => $repaymentStatus,
                'penalites' => $echeance->penalites ?? 0,
                'niveau_risque' => $repaymentStatus === 'en_retard' ? 'eleve' : 'faible',
                'methode_paiement' => $methode,
                'transaction_reference' => $reference,
            ]
        );

        $funding = $echeance->funding;
        $this->verifierCompletionFinancement($funding);

        return $echeance->fresh();
    }

    public function verifierCompletionFinancement(Funding $funding): void
    {
        $totalEcheances = $funding->echeances()->count();
        $payees = $funding->echeances()->where('statut', EcheanceStatus::PAID->value)->count();

        if ($totalEcheances > 0 && $payees >= $totalEcheances) {
            $funding->update([
                'statut' => FundingStatus::COMPLETED->value,
                'date_cloture' => now(),
            ]);

            $project = $funding->project;
            if ($project && $project->statut === ProjectStatus::ACTIVE->value) {
                $workflow = app(ProjectWorkflowService::class);
                $actorId = $funding->institution->user_id ?? 1;
                $workflow->repay($project, $actorId);
                $project = $project->fresh();
                if ($project->statut === ProjectStatus::REPAID->value) {
                    $workflow->close($project, $actorId);
                    $project = $project->fresh();
                    if ($project->statut === ProjectStatus::CLOSED->value) {
                        $workflow->transition($project, ProjectStatus::AVAILABLE_FOR_IMF, 'Projet remboursé et disponible pour nouveau financement', $actorId);
                    }
                }
            }
        }
    }

    public function getProgression(Funding $funding): array
    {
        $total = $funding->echeances()->count();
        $payees = $funding->echeancesPayees()->count();
        $retard = $funding->echeancesEnRetard()->count();
        $upcoming = $funding->echeances()->where('statut', EcheanceStatus::UPCOMING->value)->count();
        $pending = $funding->echeances()->where('statut', EcheanceStatus::PENDING->value)->count();

        $totalMontant = (float) $funding->echeances()->sum('montant_total');
        $totalPaye = (float) $funding->echeancesPayees()->sum('montant_paye');
        $totalRestant = round($totalMontant - $totalPaye, 2);

        $pourcentage = $totalMontant > 0
            ? round(($totalPaye / $totalMontant) * 100, 2)
            : 0;

        return [
            'total_echeances' => $total,
            'payees' => $payees,
            'en_retard' => $retard,
            'a_venir' => $pending,
            'prochaines' => $upcoming,
            'total_montant' => $totalMontant,
            'total_paye' => $totalPaye,
            'total_restant' => $totalRestant,
            'pourcentage' => $pourcentage,
            'termine' => $total > 0 && $payees >= $total,
        ];
    }
}
