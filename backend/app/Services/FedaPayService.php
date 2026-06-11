<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Echeance;
use App\Models\Funding;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\Transaction;
use App\Models\UserNotification;
use FedaPay\Error\SignatureVerification;
use FedaPay\FedaPay;
use FedaPay\Payout as FedaPayPayout;
use FedaPay\Transaction as FedaPayTransaction;
use FedaPay\Webhook;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FedaPayService
{
    public function __construct()
    {
        if ($this->isBypassActive()) {
            return;
        }

        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment', 'sandbox'));
    }

    public function isBypassActive(): bool
    {
        return config('services.fedapay.environment') === 'sandbox'
            && config('services.fedapay.sandbox_bypass') === true;
    }

    public function bypassPaymentUrl(Transaction $transaction, string $callbackUrl): string
    {
        return route('payment.bypass.confirm', [
            'transaction' => $transaction->id,
            'callback' => $callbackUrl,
        ]);
    }

    /**
     * Initialiser un paiement FedaPay
     *
     * @param  Project  $project  Le projet à financer
     * @param  array  $customerData  Données du client ['firstname', 'lastname', 'email', 'phone']
     * @param  string|null  $callbackUrl  URL de redirection après paiement
     * @return array ['payment_url', 'transaction_id', 'token']
     */
    public function initiatePayment(Project $project, array $customerData, ?string $callbackUrl = null): array
    {
        try {
            $isSandbox = config('services.fedapay.environment') === 'sandbox';

            // Bypass FedaPay en sandbox
            if ($this->isBypassActive()) {
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
                        'sandbox_bypass' => true,
                    ],
                ]);

                $callbackUrl = $callbackUrl ?: route('payment.callback', ['transaction_id' => $transaction->id]);

                Log::info('FedaPay bypass: payment initiated', [
                    'transaction_id' => $transaction->id,
                    'amount' => $project->montant_demande,
                ]);

                return [
                    'payment_url' => $this->bypassPaymentUrl($transaction, $callbackUrl),
                    'transaction_id' => $transaction->id,
                    'token' => 'bypass',
                ];
            }

            if ($isSandbox) {
                $customerData['phone'] = '66000001';
                $customerData['email'] = 'sandbox-'.time().'@alogoto.com';
            }

            $phoneNumber = self::normalizePhone($customerData['phone'] ?? '22997000001');

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

            // Générer l'URL de callback si non fournie
            $callbackUrl = $callbackUrl ?: route('payment.callback', ['transaction_id' => $transaction->id]);

            // Créer le client FedaPay si possible
            $fedapayCustomer = null;
            try {
                $fedapayCustomer = \FedaPay\Customer::create([
                    'firstname' => $customerData['firstname'] ?? 'Client',
                    'lastname' => $customerData['lastname'] ?? 'ALOGOTO',
                    'email' => $customerData['email'] ?? 'client@alogoto.com',
                    'phone_number' => ['number' => $phoneNumber, 'country' => 'bj'],
                ]);
            } catch (\Exception $e) {
                Log::warning('FedaPay customer creation failed (initiatePayment)', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Créer la transaction FedaPay
            $fedapayData = [
                'amount' => (int) round((float) $project->montant_demande),
                'currency' => ['iso' => 'XOF'],
                'description' => 'Financement projet: '.$project->titre,
                'customer' => $fedapayCustomer?->id
                    ? ['id' => $fedapayCustomer->id]
                    : [
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
                    'project_id' => $project->id,
                ],
            ];

            $fedapayTransaction = FedaPayTransaction::create($fedapayData);

            // Générer le token de paiement
            $token = $fedapayTransaction->generateToken(['callback_url' => $callbackUrl]);

            // Mettre à jour notre transaction avec l'ID FedaPay
            $transaction->update([
                'fedapay_transaction_id' => $fedapayTransaction->id,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'fedapay_token' => $token->token,
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
                'token' => $token->token,
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
     * @param  string|null  $callbackUrl  URL de redirection après paiement
     * @return array ['payment_url', 'transaction_id', 'token']
     */
    public function initierPaiementEcheanceViaFedaPay(Echeance $echeance, array $customerData, ?float $montant = null, ?string $callbackUrl = null): array
    {
        $montantPaiement = $montant ?? (float) $echeance->montant_restant;

        // Bypass FedaPay en sandbox
        if ($this->isBypassActive()) {
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
                    'sandbox_bypass' => true,
                ],
            ]);

            $callbackUrl = $callbackUrl ?: route('payment.callback', ['transaction_id' => $transaction->id]);

            Log::info('FedaPay bypass: echeance payment initiated', [
                'transaction_id' => $transaction->id,
                'amount' => $montantPaiement,
            ]);

            return [
                'payment_url' => $this->bypassPaymentUrl($transaction, $callbackUrl),
                'transaction_id' => $transaction->id,
                'token' => 'bypass',
            ];
        }

        $montantInt = (int) round($montantPaiement);

        // En mode sandbox, utiliser un numéro de test FedaPay
        $isSandbox = config('services.fedapay.environment') === 'sandbox';
        if ($isSandbox) {
            $customerData['phone'] = '66000001'; // MTN test number
        }

        $phoneNumber = self::normalizePhone($customerData['phone'] ?? '22997000001');
        $customerEmail = $customerData['email'] ?? 'client@alogoto.com';

        // En sandbox, forcer la création d'un nouveau client FedaPay pour garantir le téléphone
        if ($isSandbox) {
            $customerEmail = 'sandbox-'.time().'@alogoto.com';
        }

        $fedapayCustomer = null;
        try {
            $fedapayCustomer = \FedaPay\Customer::create([
                'firstname' => $customerData['firstname'] ?? 'Client',
                'lastname' => $customerData['lastname'] ?? 'ALOGOTO',
                'email' => $customerEmail,
                'phone_number' => ['number' => $phoneNumber, 'country' => 'bj'],
            ]);
        } catch (\Exception $e) {
            Log::warning('FedaPay customer creation failed, using inline', [
                'error' => $e->getMessage(),
            ]);
        }

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

            // Générer l'URL de callback si non fournie
            $callbackUrl = $callbackUrl ?: route('payment.callback', ['transaction_id' => $transaction->id]);

            // Créer la transaction FedaPay en référençant le client existant
            $fedapayData = [
                'amount' => $montantInt,
                'currency' => ['iso' => 'XOF'],
                'description' => 'Remboursement échéance #'.$echeance->numero_echeance.
                    ' - '.($echeance->project?->titre ?? 'Projet'),
                'customer' => $fedapayCustomer?->id
                    ? ['id' => $fedapayCustomer->id]
                    : [
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
                    'echeance_id' => $echeance->id,
                    'type' => 'echeance_repayment',
                ],
            ];

            $fedapayTransaction = FedaPayTransaction::create($fedapayData);

            // Générer le token de paiement
            $token = $fedapayTransaction->generateToken(['callback_url' => $callbackUrl]);

            // Mettre à jour notre transaction avec l'ID FedaPay
            $transaction->update([
                'fedapay_transaction_id' => $fedapayTransaction->id,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'fedapay_token' => $token->token,
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
                'token' => $token->token,
            ];

        } catch (\Exception $e) {
            $errorBody = '';
            if (method_exists($e, 'getHttpBody')) {
                $errorBody = $e->getHttpBody();
            } elseif ($e->getPrevious() && method_exists($e->getPrevious(), 'getHttpBody')) {
                $errorBody = $e->getPrevious()->getHttpBody();
            }

            Log::error('FedaPay echeance payment initiation failed', [
                'echeance_id' => $echeance->id,
                'error' => $e->getMessage(),
                'http_body' => $errorBody,
            ]);

            throw new RuntimeException('Échec de l\'initialisation du paiement: '.$e->getMessage());
        }
    }

    /**
     * Initier un décaissement FedaPay (Payout) vers le porteur
     *
     * @param  Funding  $funding  Le financement à décaisser
     * @return array Résultat du décaissement
     */
    public function initierDecaissement(Funding $funding): array
    {
        $porteur = $funding->project->owner;
        $montant = (float) ($funding->montant_propose ?: $funding->montant);
        $institution = $funding->institution;

        // Créer la transaction locale de type disbursement
        $transaction = Transaction::create([
            'project_id' => $funding->project_id,
            'user_id' => $porteur->id,
            'institution_id' => $funding->institution_id,
            'type' => 'disbursement',
            'amount' => $montant,
            'currency' => 'XOF',
            'status' => 'pending',
            'metadata' => [
                'financement_id' => $funding->id,
                'project_title' => $funding->project->titre,
                'institution_name' => $institution?->nom ?? 'Institution',
                'initiated_at' => now()->toIso8601String(),
            ],
        ]);

        try {
            // Créer le payout FedaPay
            $callbackUrl = route('payment.callback', ['transaction_id' => $transaction->id]);
            $payoutData = [
                'amount' => (int) round($montant),
                'currency' => ['iso' => 'XOF'],
                'description' => 'Décaissement #'.$funding->id.' - '.$funding->project->titre,
                'customer' => [
                    'firstname' => $porteur->prenom ?? explode(' ', $porteur->name)[0],
                    'lastname' => $porteur->nom ?? explode(' ', $porteur->name)[1] ?? 'Client',
                    'email' => $porteur->email,
                    'phone_number' => [
                        'number' => self::normalizePhone($porteur->telephone ?? '22997000001'),
                        'country' => 'bj',
                    ],
                ],
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'financement_id' => $funding->id,
                ],
            ];

            $payout = FedaPayPayout::create($payoutData);

            // Envoyer le payout immédiatement
            $payout->sendNow();

            // Mettre à jour la transaction locale
            $transaction->update([
                'fedapay_transaction_id' => $payout->id,
                'status' => 'processing',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'fedapay_payout_id' => $payout->id,
                    'payout_status' => $payout->status,
                ]),
            ]);

            // Notifier le porteur
            UserNotification::create([
                'user_id' => $porteur->id,
                'type' => 'decaissement_effectue',
                'title' => 'Décaissement en cours',
                'content' => 'Un décaissement de '.number_format($montant, 0, ',', ' ').
                    ' FCFA pour le projet "'.$funding->project->titre.
                    '" est en cours de traitement via FedaPay.',
                'is_read' => false,
            ]);

            if ($institution?->user_id) {
                UserNotification::create([
                    'user_id' => $institution->user_id,
                    'type' => 'decaissement_envoye',
                    'title' => 'Décaissement envoyé',
                    'content' => 'Le décaissement de '.number_format($montant, 0, ',', ' ').
                        ' FCFA pour le projet "'.$funding->project->titre.'" a été initié via FedaPay.',
                    'is_read' => false,
                ]);
            }

            Log::info('FedaPay disbursement initiated', [
                'financement_id' => $funding->id,
                'transaction_id' => $transaction->id,
                'payout_id' => $payout->id,
                'amount' => $montant,
                'status' => $payout->status,
            ]);

            return [
                'success' => true,
                'transaction_id' => $transaction->id,
                'payout_id' => $payout->id,
            ];

        } catch (\Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'failed_at' => now()->toIso8601String(),
                    'failure_reason' => $e->getMessage(),
                ]),
            ]);

            Log::error('FedaPay disbursement failed', [
                'financement_id' => $funding->id,
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Échec du décaissement FedaPay: '.$e->getMessage());
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
     * Vérifier le statut FedaPay et confirmer la transaction localement
     * (utilisé par le callback quand le webhook n'a pas été reçu)
     */
    public function verifyAndConfirmTransaction(Transaction $transaction, string $fedapayId): bool
    {
        $status = $this->checkTransactionStatus($fedapayId);

        if (($status['status'] ?? '') === 'approved') {
            Log::info('FedaPay transaction confirmed via callback', [
                'transaction_id' => $transaction->id,
                'fedapay_id' => $fedapayId,
            ]);

            $transaction->update([
                'fedapay_transaction_id' => $fedapayId,
            ]);

            $this->handleTransactionCompleted(['id' => (int) $fedapayId]);

            return true;
        }

        return false;
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

                case 'payout.sent':
                    return $this->handlePayoutSent($eventData);

                case 'payout.failed':
                    return $this->handlePayoutFailed($eventData);

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
     * Gérer payout envoyé avec succès
     */
    private function handlePayoutSent(array $eventData): bool
    {
        $payoutId = $eventData['id'] ?? null;

        if (! $payoutId) {
            Log::error('FedaPay payout webhook missing payout ID');

            return false;
        }

        $transaction = Transaction::where('fedapay_transaction_id', $payoutId)->first();

        if (! $transaction) {
            Log::error('Disbursement transaction not found', ['payout_id' => $payoutId]);

            return false;
        }

        $transaction->update([
            'status' => 'completed',
            'paid_at' => now(),
            'metadata' => array_merge($transaction->metadata ?? [], [
                'completed_at' => now()->toIso8601String(),
                'payout_status' => 'sent',
                'fedapay_data' => $eventData,
            ]),
        ]);

        Log::info('FedaPay payout sent successfully', [
            'payout_id' => $payoutId,
            'transaction_id' => $transaction->id,
        ]);

        return true;
    }

    /**
     * Gérer payout échoué
     */
    private function handlePayoutFailed(array $eventData): bool
    {
        $payoutId = $eventData['id'] ?? null;

        $transaction = Transaction::where('fedapay_transaction_id', $payoutId)->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'failed_at' => now()->toIso8601String(),
                    'payout_status' => 'failed',
                    'failure_reason' => $eventData['last_error_message'] ?? 'Erreur inconnue',
                    'fedapay_data' => $eventData,
                ]),
            ]);

            Log::warning('FedaPay payout failed', [
                'payout_id' => $payoutId,
                'transaction_id' => $transaction->id,
                'reason' => $eventData['last_error_message'] ?? 'Unknown',
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

    private static function normalizePhone(?string $phone): string
    {
        if ($phone === null) {
            return '22997000001';
        }

        return preg_replace('/[^0-9]/', '', $phone);
    }
}
