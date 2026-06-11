<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Repayment;
use App\Models\Transaction;
use App\Models\User;
use FedaPay\FedaPay;
use FedaPay\Transaction as FedaPayTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepaymentService
{
    /**
     * Récupérer les remboursements du porteur connecté
     */
    public function getPorteurRepayments(User $user, array $filters = []): array
    {
        $query = Repayment::with(['project', 'institution'])
            ->whereHas('project', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        if (! empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (! empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        $repayments = $query->orderBy('date_echeance', 'asc')->get();

        return [
            'repayments' => $repayments,
            'stats' => $this->calculateStats($repayments),
        ];
    }

    /**
     * Calculer les statistiques de remboursement
     */
    public function calculateStats($repayments): array
    {
        $totalRembourse = $repayments->where('statut', 'paye')->sum('montant_total');
        $totalRestant = $repayments->where('statut', '!=', 'paye')->sum('montant_restant');
        $totalEcheances = $repayments->count();
        $payes = $repayments->where('statut', 'paye')->count();
        $enRetard = $repayments->where('statut', 'en_retard')->count();

        $tauxRemboursement = $totalEcheances > 0 ? round(($payes / $totalEcheances) * 100, 2) : 0;

        return [
            'total_rembourse' => $totalRembourse,
            'total_restant' => $totalRestant,
            'taux_remboursement' => $tauxRemboursement,
            'en_retard' => $enRetard,
            'total_echeances' => $totalEcheances,
        ];
    }

    /**
     * Initier un paiement de remboursement via FedaPay
     */
    public function initiateRepaymentPayment(Repayment $repayment, User $user, array $customerData = [], ?string $callbackUrl = null): array
    {
        // Vérifier que le remboursement appartient au porteur
        if ($repayment->project->user_id !== $user->id) {
            throw new \RuntimeException('Accès non autorisé à ce remboursement.');
        }

        // Vérifier que le remboursement n'est pas déjà payé
        if ($repayment->statut === 'paye') {
            throw new \RuntimeException('Ce remboursement est déjà payé.');
        }

        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment', 'sandbox'));

        $customerData = array_merge([
            'firstname' => $user->prenom ?? explode(' ', $user->name)[0],
            'lastname' => $user->nom ?? explode(' ', $user->name)[1] ?? 'Client',
            'email' => $user->email,
            'phone' => $user->telephone ?? '22997000001',
        ], $customerData);

        $montant = $repayment->montant_restant ?: $repayment->montant_total;
        $isSandbox = config('services.fedapay.environment') === 'sandbox';

        // Bypass FedaPay en sandbox
        if ($isSandbox && config('services.fedapay.sandbox_bypass') === true) {
            $transaction = Transaction::create([
                'project_id' => $repayment->project_id,
                'user_id' => $user->id,
                'institution_id' => $repayment->institution_id,
                'type' => 'repayment',
                'amount' => $montant,
                'currency' => 'XOF',
                'status' => 'pending',
                'metadata' => [
                    'repayment_id' => $repayment->id,
                    'initiated_at' => now()->toIso8601String(),
                    'sandbox_bypass' => true,
                ],
            ]);

            $callbackUrl = $callbackUrl ?: route('payment.callback', ['transaction_id' => $transaction->id]);

            Log::info('FedaPay bypass: repayment payment initiated', [
                'transaction_id' => $transaction->id,
                'amount' => $montant,
            ]);

            return [
                'payment_url' => route('payment.bypass.confirm', [
                    'transaction' => $transaction->id,
                    'callback' => $callbackUrl,
                ]),
                'transaction_id' => $transaction->id,
                'token' => 'bypass',
            ];
        }

        if ($isSandbox) {
            $customerData['phone'] = '66000001';
            $customerData['email'] = 'sandbox-'.time().'@alogoto.com';
        }

        $phoneNumber = self::normalizePhone($customerData['phone'] ?? '22997000001');

        // Créer une transaction de type 'repayment'
        $transaction = Transaction::create([
            'project_id' => $repayment->project_id,
            'user_id' => $user->id,
            'institution_id' => $repayment->institution_id,
            'type' => 'repayment',
            'amount' => $montant,
            'currency' => 'XOF',
            'status' => 'pending',
            'metadata' => [
                'repayment_id' => $repayment->id,
                'initiated_at' => now()->toIso8601String(),
            ],
        ]);

        try {
            // Générer l'URL de callback si non fournie
            $callbackUrl = $callbackUrl ?: route('payment.callback', ['transaction_id' => $transaction->id]);

            // Créer la transaction FedaPay avec le bon montant (pas le montant_demande du projet)
            $fedapayData = [
                'amount' => (int) round($montant),
                'currency' => ['iso' => 'XOF'],
                'description' => 'Remboursement projet: '.$repayment->project->titre,
                'customer' => [
                    'firstname' => $customerData['firstname'] ?? 'Client',
                    'lastname' => $customerData['lastname'] ?? 'ALOGOTO',
                    'email' => $customerData['email'] ?? 'client@alogoto.com',
                    'phone_number' => [
                        'number' => self::normalizePhone($customerData['phone'] ?? '22997000001'),
                        'country' => 'bj',
                    ],
                ],
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'repayment_id' => $repayment->id,
                    'type' => 'repayment',
                ],
            ];

            $fedapayTransaction = FedaPayTransaction::create($fedapayData);

            $token = $fedapayTransaction->generateToken(['callback_url' => $callbackUrl]);

            $transaction->update([
                'fedapay_transaction_id' => $fedapayTransaction->id,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'fedapay_token' => $token->token,
                ]),
            ]);

            Log::info('Repayment payment initiated', [
                'repayment_id' => $repayment->id,
                'transaction_id' => $transaction->id,
                'fedapay_id' => $fedapayTransaction->id,
                'amount' => $montant,
            ]);

            return [
                'payment_url' => $token->url,
                'transaction_id' => $transaction->id,
                'token' => $token->token,
            ];

        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Confirmer un paiement de remboursement
     */
    public function confirmRepaymentPayment(Repayment $repayment, Transaction $transaction, array $webhookData = []): void
    {
        DB::transaction(function () use ($repayment, $transaction, $webhookData) {
            // Mettre à jour le remboursement
            $repayment->update([
                'statut' => 'paye',
                'date_paiement' => now(),
                'montant_restant' => 0,
                'methode_paiement' => $webhookData['payment_method'] ?? 'fedapay',
                'transaction_reference' => $transaction->fedapay_transaction_id,
                'commentaires' => 'Paiement confirmé via FedaPay',
            ]);

            // Mettre à jour la transaction
            $transaction->markAsCompleted();
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'completed_at' => now()->toIso8601String(),
                    'fedapay_data' => $webhookData,
                ]),
            ]);

            // Enregistrer l'historique
            $this->logHistory($repayment, 'paid', $transaction->user_id, [
                'amount' => $repayment->montant_total,
                'method' => $repayment->methode_paiement,
                'transaction_id' => $transaction->id,
            ]);

            // Vérifier si le projet est entièrement remboursé
            $this->checkProjectCompletion($repayment->project);
        });
    }

    /**
     * Enregistrer dans l'historique
     */
    public function logHistory(Repayment $repayment, string $action, int $acteurId, array $details = []): void
    {
        DB::table('repayment_histories')->insert([
            'repayment_id' => $repayment->id,
            'action' => $action,
            'acteur_type' => 'App\Models\User',
            'acteur_id' => $acteurId,
            'details' => json_encode($details),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Récupérer l'historique d'un remboursement
     */
    public function getHistory(Repayment $repayment): array
    {
        $histories = DB::table('repayment_histories')
            ->where('repayment_id', $repayment->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->details = json_decode($item->details, true);

                return $item;
            });

        return $histories->toArray();
    }

    /**
     * Vérifier si le projet est entièrement remboursé
     */
    private function checkProjectCompletion(Project $project): void
    {
        $targetAmount = max((float) $project->montant_finance, (float) $project->montant_demande);
        $paidAmount = (float) Repayment::where('project_id', $project->id)
            ->where('statut', 'paye')
            ->sum('montant_total');

        if ($targetAmount > 0 && $paidAmount >= $targetAmount) {
            app(ProjectWorkflowService::class)->complete($project, $project->user_id);
        }
    }

    /**
     * Calculer les pénalités pour retard
     */
    public function calculatePenalty(Repayment $repayment): float
    {
        if ($repayment->statut !== 'en_retard' || ! $repayment->date_echeance) {
            return 0;
        }

        $daysLate = now()->diffInDays($repayment->date_echeance);
        $dailyRate = config('services.fedapay.penalty_daily_rate', 0.001);

        return round((float) $repayment->montant_total * $dailyRate * $daysLate, 2);
    }

    private static function normalizePhone(?string $phone): string
    {
        if ($phone === null) {
            return '22997000001';
        }

        return preg_replace('/[^0-9]/', '', $phone);
    }
}
