<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Echeance;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\Transaction;
use App\Models\UserNotification;
use FedaPay\Error\SignatureVerification;
use FedaPay\FedaPay;
use FedaPay\Transaction as FedaPayTransaction;
use FedaPay\Webhook;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FedaPayService
{
    public function __construct()
    {
        // Configuration FedaPay
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment', 'sandbox'));
    }

    /**
     * Initialiser un paiement FedaPay
     *
     * @param  Project  $project  Le projet à financer
     * @param  array  $customerData  Données du client ['firstname', 'lastname', 'email', 'phone']
     * @return array ['payment_url', 'transaction_id', 'token']
     */
    public function initiatePayment(Project $project, array $customerData): array
    {
        try {
            // Créer la transaction dans notre base
            $transaction = Transaction::create([
                'project_id' => $project->id,
                'user_id' => $project->user_id,
                'institution_id' => null,
                'type' => 'payment',
                'amount' => $project->montant_demande,
                'currency' => 'XOF',
                'status' => 'pending',
                'metadata' => [
                    'project_title' => $project->titre,
                    'initiated_at' => now()->toIso8601String(),
                ],
            ]);

            // Créer la transaction FedaPay
            $fedapayTransaction = FedaPayTransaction::create([
                'amount' => (string) $project->montant_demande,
                'currency' => ['iso' => 'XOF'],
                'description' => 'Financement projet: '.$project->titre,
                'customer' => [
                    'firstname' => $customerData['firstname'] ?? 'Client',
                    'lastname' => $customerData['lastname'] ?? 'ALOGOTO',
                    'email' => $customerData['email'] ?? 'client@alogoto.com',
                    'phone_number' => [
                        'number' => $customerData['phone'] ?? '22900000000',
                        'country' => 'bj',
                    ],
                ],
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'project_id' => $project->id,
                ],
            ]);

            // Générer le token de paiement
            $token = $fedapayTransaction->generateToken();

            // Mettre à jour notre transaction avec l'ID FedaPay
            $transaction->update([
                'fedapay_transaction_id' => $fedapayTransaction->id,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'fedapay_token' => $token->id,
                ]),
            ]);

            Log::info('FedaPay payment initiated', [
                'transaction_id' => $transaction->id,
                'fedapay_id' => $fedapayTransaction->id,
                'amount' => $project->montant_demande,
            ]);

            return [
                'payment_url' => $token->url,
                'transaction_id' => $transaction->id,
                'token' => $token->id,
            ];

        } catch (\Exception $e) {
            Log::error('FedaPay payment initiation failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Échec de l\'initialisation du paiement: '.$e->getMessage());
        }
    }

    /**
     * Initialiser un paiement d'échéance via FedaPay
     *
     * @param  Echeance  $echeance  L'échéance à payer
     * @param  array  $customerData  Données du client ['firstname', 'lastname', 'email', 'phone']
     * @param  float|null  $montant  Montant à payer (null = montant_restant)
     * @return array ['payment_url', 'transaction_id', 'token']
     */
    public function initierPaiementEcheanceViaFedaPay(Echeance $echeance, array $customerData, ?float $montant = null): array
    {
        $montantPaiement = $montant ?? (float) $echeance->montant_restant;

        try {
            // Créer la transaction dans notre base
            $transaction = Transaction::create([
                'project_id' => $echeance->project_id,
                'user_id' => $echeance->project->user_id,
                'institution_id' => $echeance->institution_id,
                'type' => 'echeance_repayment',
                'amount' => $montantPaiement,
                'currency' => 'XOF',
                'status' => 'pending',
                'metadata' => [
                    'echeance_id' => $echeance->id,
                    'financement_id' => $echeance->financement_id,
                    'numero_echeance' => $echeance->numero_echeance,
                    'project_title' => $echeance->project?->titre ?? 'Projet',
                    'initiated_at' => now()->toIso8601String(),
                ],
            ]);

            // Créer la transaction FedaPay
            $fedapayTransaction = FedaPayTransaction::create([
                'amount' => (string) $montantPaiement,
                'currency' => ['iso' => 'XOF'],
                'description' => 'Remboursement échéance #'.$echeance->numero_echeance.
                    ' - '.($echeance->project?->titre ?? 'Projet'),
                'customer' => [
                    'firstname' => $customerData['firstname'] ?? 'Client',
                    'lastname' => $customerData['lastname'] ?? 'ALOGOTO',
                    'email' => $customerData['email'] ?? 'client@alogoto.com',
                    'phone_number' => [
                        'number' => $customerData['phone'] ?? '22900000000',
                        'country' => 'bj',
                    ],
                ],
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'echeance_id' => $echeance->id,
                    'type' => 'echeance_repayment',
                ],
            ]);

            // Générer le token de paiement
            $token = $fedapayTransaction->generateToken();

            // Mettre à jour notre transaction avec l'ID FedaPay
            $transaction->update([
                'fedapay_transaction_id' => $fedapayTransaction->id,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'fedapay_token' => $token->id,
                ]),
            ]);

            Log::info('FedaPay echeance payment initiated', [
                'echeance_id' => $echeance->id,
                'transaction_id' => $transaction->id,
                'fedapay_id' => $fedapayTransaction->id,
                'amount' => $montantPaiement,
            ]);

            return [
                'payment_url' => $token->url,
                'transaction_id' => $transaction->id,
                'token' => $token->id,
            ];

        } catch (\Exception $e) {
            Log::error('FedaPay echeance payment initiation failed', [
                'echeance_id' => $echeance->id,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Échec de l\'initialisation du paiement: '.$e->getMessage());
        }
    }

    /**
     * Vérifier le statut d'une transaction FedaPay
     */
    public function checkTransactionStatus(string $fedapayTransactionId): array
    {
        try {
            $fedapayTransaction = FedaPayTransaction::retrieve($fedapayTransactionId);

            return [
                'status' => $fedapayTransaction->status,
                'amount' => $fedapayTransaction->amount,
                'currency' => $fedapayTransaction->currency['iso'] ?? 'XOF',
                'paid_at' => $fedapayTransaction->paid_at,
            ];

        } catch (\Exception $e) {
            Log::error('FedaPay status check failed', [
                'fedapay_id' => $fedapayTransactionId,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Échec de la vérification: '.$e->getMessage());
        }
    }

    /**
     * Traiter le webhook FedaPay
     *
     * @param  string  $rawPayload  Données brutes du webhook
     * @param  string|null  $signature  Signature du webhook
     * @return bool Succès du traitement
     */
    public function handleWebhook(string $rawPayload, ?string $signature): bool
    {
        $payload = null;
        try {
            $payload = $this->verifyWebhookSignature($rawPayload, $signature);
            $eventType = $payload['type'] ?? '';
            $eventData = $payload['data'] ?? [];

            Log::info('FedaPay webhook received', [
                'event_type' => $eventType,
                'event_id' => $payload['id'] ?? null,
            ]);

            switch ($eventType) {
                case 'transaction.completed':
                    return $this->handleTransactionCompleted($eventData);

                case 'transaction.failed':
                    return $this->handleTransactionFailed($eventData);

                case 'transaction.refunded':
                    return $this->handleTransactionRefunded($eventData);

                default:
                    Log::warning('Unhandled FedaPay webhook event', ['type' => $eventType]);

                    return true; // Accepter pour éviter les retries
            }

        } catch (\Exception $e) {
            Log::error('FedaPay webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return false;
        }
    }

    /**
     * Vérifier la signature du webhook
     */
    private function verifyWebhookSignature(string $rawPayload, ?string $signature): array
    {
        $webhookSecret = config('services.fedapay.webhook_secret');

        if (empty($webhookSecret)) {
            throw new RuntimeException('FEDAPAY_WEBHOOK_SECRET non configuré.');
        }

        if ($signature === null || $signature === '') {
            throw new RuntimeException('Signature webhook absente.');
        }

        try {
            $event = Webhook::constructEvent($rawPayload, $signature, $webhookSecret);

            return $event->__toArray(true);
        } catch (SignatureVerification $e) {
            Log::warning('FedaPay webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Signature webhook invalide.', 0, $e);
        } catch (\UnexpectedValueException $e) {
            Log::warning('FedaPay webhook payload invalid', [
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Payload webhook invalide.', 0, $e);
        }
    }

    /**
     * Gérer transaction complétée
     */
    private function handleTransactionCompleted(array $eventData): bool
    {
        $fedapayId = $eventData['id'] ?? null;

        if (! $fedapayId) {
            Log::error('FedaPay webhook missing transaction ID');

            return false;
        }

        // Trouver la transaction dans notre base
        $transaction = Transaction::where('fedapay_transaction_id', $fedapayId)->first();

        if (! $transaction) {
            Log::error('Transaction not found in database', ['fedapay_id' => $fedapayId]);

            return false;
        }

        // Vérifier l'idempotence (éviter double traitement)
        if ($transaction->isCompleted()) {
            Log::info('Transaction already completed, skipping', [
                'transaction_id' => $transaction->id,
            ]);

            return true;
        }

        // Mettre à jour la transaction
        $transaction->markAsCompleted();
        $transaction->update([
            'fedapay_payment_method' => $eventData['payment_method'] ?? null,
            'metadata' => array_merge($transaction->metadata ?? [], [
                'completed_at' => now()->toIso8601String(),
                'fedapay_data' => $eventData,
            ]),
        ]);

        // Si c'est un remboursement (Repayment model)
        if ($transaction->type === 'repayment' && isset($transaction->metadata['repayment_id'])) {
            $repayment = Repayment::find($transaction->metadata['repayment_id']);
            if ($repayment) {
                $repaymentService = new \App\Services\RepaymentService;
                $repaymentService->confirmRepaymentPayment($repayment, $transaction, $eventData);
                Log::info('Repayment payment confirmed via webhook', [
                    'repayment_id' => $repayment->id,
                    'transaction_id' => $transaction->id,
                ]);
            }

            return true;
        }

        // Si c'est un paiement d'échéance
        if ($transaction->type === 'echeance_repayment' && isset($transaction->metadata['echeance_id'])) {
            $echeance = Echeance::find($transaction->metadata['echeance_id']);
            if ($echeance) {
                $echeanceService = new \App\Services\EcheanceService;
                $echeanceService->enregistrerPaiement(
                    $echeance,
                    (float) $transaction->amount,
                    'fedapay',
                    $transaction->fedapay_transaction_id
                );

                $transaction->fresh()->markAsCompleted();

                // Créer les notifications
                $montant = number_format((float) $transaction->amount, 0, ',', ' ');
                $projetTitre = $echeance->project?->titre ?? 'Projet';

                UserNotification::create([
                    'user_id' => $echeance->project->user_id,
                    'type' => 'remboursement_effectue',
                    'title' => 'Paiement d\'échéance confirmé',
                    'content' => 'Votre paiement de '.$montant.' FCFA pour l\'échéance #'.
                        $echeance->numero_echeance.' du projet "'.$projetTitre.'" a été confirmé.',
                    'is_read' => false,
                ]);

                if ($echeance->funding?->institution?->user_id) {
                    UserNotification::create([
                        'user_id' => $echeance->funding->institution->user_id,
                        'type' => 'remboursement_recu',
                        'title' => 'Remboursement reçu',
                        'content' => 'Un paiement de '.$montant.' FCFA a été reçu pour l\'échéance #'.
                            $echeance->numero_echeance.' du projet "'.$projetTitre.'".',
                        'is_read' => false,
                    ]);
                }

                Log::info('Echeance payment confirmed via FedaPay webhook', [
                    'echeance_id' => $echeance->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                ]);
            }

            return true;
        }

        // Sinon, c'est un financement de projet (logique existante)
        if ($transaction->type === 'payment') {
            // Mettre à jour le projet si nécessaire
            $project = $transaction->project;
            if ($project) {
                $project->increment('montant_finance', $transaction->amount);

                $status = ProjectStatus::fromStorageOr($project->statut);
                if ($status === ProjectStatus::INSTITUTION_ACCEPTED) {
                    app(ProjectWorkflowService::class)->markAsFunded($project, $transaction->user_id);
                }

                Log::info('Project funding updated via FedaPay webhook', [
                    'project_id' => $project->id,
                    'amount' => $transaction->amount,
                ]);
            }
        }

        return true;
    }

    /**
     * Gérer transaction échouée
     */
    private function handleTransactionFailed(array $eventData): bool
    {
        $fedapayId = $eventData['id'] ?? null;

        $transaction = Transaction::where('fedapay_transaction_id', $fedapayId)->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'failed_at' => now()->toIso8601String(),
                    'failure_reason' => $eventData['reason'] ?? 'Unknown',
                ]),
            ]);

            Log::warning('FedaPay transaction failed', [
                'transaction_id' => $transaction->id,
                'reason' => $eventData['reason'] ?? 'Unknown',
            ]);
        }

        return true;
    }

    /**
     * Gérer transaction remboursée
     */
    private function handleTransactionRefunded(array $eventData): bool
    {
        $fedapayId = $eventData['id'] ?? null;

        $transaction = Transaction::where('fedapay_transaction_id', $fedapayId)->first();

        if ($transaction) {
            // Créer une nouvelle transaction de type refund
            Transaction::create([
                'project_id' => $transaction->project_id,
                'user_id' => $transaction->user_id,
                'type' => 'refund',
                'amount' => $eventData['amount'] ?? $transaction->amount,
                'currency' => 'XOF',
                'status' => 'completed',
                'fedapay_transaction_id' => $eventData['refund_id'] ?? null,
                'metadata' => [
                    'original_transaction_id' => $transaction->id,
                    'refunded_at' => now()->toIso8601String(),
                ],
            ]);

            Log::info('FedaPay transaction refunded', [
                'original_transaction_id' => $transaction->id,
                'refund_amount' => $eventData['amount'] ?? $transaction->amount,
            ]);
        }

        return true;
    }

    /**
     * Obtenir les méthodes de paiement disponibles
     */
    public function getPaymentMethods(): array
    {
        return [
            'mobile_money' => [
                'name' => 'Mobile Money',
                'providers' => ['MTN', 'Moov', 'Celtiis'],
                'enabled' => true,
            ],
            'card' => [
                'name' => 'Carte bancaire',
                'providers' => ['Visa', 'Mastercard'],
                'enabled' => true,
            ],
        ];
    }
}
