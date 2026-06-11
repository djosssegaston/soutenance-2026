<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\FedaPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected FedaPayService $fedaPay
    ) {}

    /**
     * Afficher la page de paiement pour un projet
     */
    public function showPaymentForm(Project $project)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if (auth()->id() !== $project->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à financer ce projet.');
        }

        $paymentMethods = $this->fedaPay->getPaymentMethods();

        return view('payment.form', [
            'project' => $project,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Initialiser le paiement FedaPay
     */
    public function initiatePayment(Request $request, Project $project)
    {
        // Vérifier l'autorisation
        if (auth()->id() !== $project->user_id) {
            abort(403, 'Non autorisé.');
        }

        // Validation des données client
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:mobile_money,card',
        ]);

        try {
            // Initialiser le paiement
            $result = $this->fedaPay->initiatePayment($project, [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            // Rediriger vers la page de paiement FedaPay
            return redirect($result['payment_url']);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Échec de l\'initialisation du paiement. Veuillez réessayer.',
            ]);
        }
    }

    /**
     * Page de succès après paiement
     */
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->get('transaction_id');

        if (! $transactionId) {
            return redirect()->route('dashboard.porteur')
                ->with('warning', 'Transaction non trouvée.');
        }

        return view('payment.success', [
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Page d'échec de paiement
     */
    public function paymentFailed(Request $request)
    {
        $returnUrl = $request->get('return_url', url('frontend/dashboard02/porteur'));

        return view('payment.failed', [
            'message' => $request->get('message', 'Le paiement a échoué.'),
            'return_url' => $returnUrl,
        ]);
    }

    /**
     * Webhook FedaPay (appelé par FedaPay automatiquement)
     *
     * Cette route doit être publique car FedaPay n'est pas authentifié
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-FedaPay-Signature');

        Log::info('FedaPay webhook received', [
            'signature' => $signature,
            'content_length' => strlen($payload),
        ]);

        try {
            // Traiter le webhook via le service
            $success = $this->fedaPay->handleWebhook($payload, $signature);

            if ($success) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['status' => 'error', 'message' => 'Signature ou payload invalide.'], 400);

        } catch (\Exception $e) {
            Log::error('FedaPay webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Callback après paiement FedaPay (redirection utilisateur)
     */
    public function callback(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status', 'completed');
        $fedapayId = $request->get('id');

        if (! $transactionId) {
            return redirect()->route('payment.failed', ['message' => 'Transaction non spécifiée.']);
        }

        $transaction = \App\Models\Transaction::find($transactionId);

        // Si FedaPay indique que le paiement a échoué
        if ($status === 'failed') {
            $returnUrl = $transaction
                ? match ($transaction->type) {
                    'echeance_repayment' => '/frontend/dashboard02/porteur/echances.php',
                    'repayment' => '/frontend/dashboard02/porteur/remboursements.php',
                    default => '/frontend/dashboard02/porteur',
                }
            : '/frontend/dashboard02/porteur';

            return redirect()->route('payment.failed', [
                'message' => 'La transaction a été refusée. Veuillez réessayer avec un autre moyen de paiement.',
                'return_url' => $returnUrl,
            ]);
        }

        // Si FedaPay indique succès: vérifier et confirmer
        if ($status === 'completed' && $transaction && ! $transaction->isCompleted()) {
            try {
                $fedapayTxnId = $fedapayId ?? $transaction->fedapay_transaction_id;
                if ($fedapayTxnId) {
                    $this->fedaPay->verifyAndConfirmTransaction($transaction, $fedapayTxnId);
                }
            } catch (\Exception $e) {
                Log::warning('FedaPay callback verification failed', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Transaction confirmée
        if ($status === 'completed' && $transaction && $transaction->isCompleted()) {
            $returnUrl = match ($transaction->type) {
                'echeance_repayment' => '/frontend/dashboard02/porteur/echances.php',
                'repayment' => '/frontend/dashboard02/porteur/remboursement.php',
                default => '/frontend/dashboard02/porteur',
            };

            return redirect($returnUrl)
                ->with('success', 'Paiement confirmé !');
        }

        $returnUrl = $transaction
            ? match ($transaction->type) {
                'echeance_repayment' => '/frontend/dashboard02/porteur/echances.php',
                'repayment' => '/frontend/dashboard02/porteur/remboursement.php',
                default => '/frontend/dashboard02/porteur',
            }
        : '/frontend/dashboard02/porteur';

        return redirect()->route('payment.failed', [
            'return_url' => $returnUrl,
        ]);
    }

    /**
     * Bypass FedaPay sandbox - page de confirmation de test
     */
    public function bypassConfirm(Request $request, \App\Models\Transaction $transaction)
    {
        if ($request->isMethod('post')) {
            $phone = preg_replace('/\D/', '', $request->input('phone', ''));

            // Numéro de test pour simuler un échec (solde insuffisant)
            if ($phone === '0143440858') {
                $transaction->update(['status' => 'failed']);

                // Marquer l'échéance comme échouée
                if ($transaction->type === 'echeance_repayment' && isset($transaction->metadata['echeance_id'])) {
                    $echeance = \App\Models\Echeance::find($transaction->metadata['echeance_id']);
                    if ($echeance) {
                        $echeance->update(['statut' => \App\Enums\EcheanceStatus::CANCELLED->value]);
                    }
                }

                Log::info('FedaPay bypass: transaction failed (insufficient balance)', [
                    'transaction_id' => $transaction->id,
                    'phone' => $phone,
                ]);

                $returnUrl = match ($transaction->type) {
                    'echeance_repayment' => '/frontend/dashboard02/porteur/echances.php',
                    'repayment' => '/frontend/dashboard02/porteur/remboursement.php',
                    'disbursement' => '/frontend/dashboard02/institution/financement.php',
                    default => '/frontend/dashboard02/porteur',
                };

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Paiement échoué : solde insuffisant.',
                        'redirect_url' => $returnUrl,
                    ]);
                }

                return redirect($returnUrl)
                    ->with('error', 'Paiement échoué : solde insuffisant.');
            }

            // Succès
            $transaction->markAsCompleted();
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'completed_at' => now()->toIso8601String(),
                    'sandbox_bypass' => true,
                    'phone' => $phone,
                ]),
            ]);

            if ($transaction->type === 'repayment' && isset($transaction->metadata['repayment_id'])) {
                $repayment = \App\Models\Repayment::find($transaction->metadata['repayment_id']);
                if ($repayment) {
                    $repaymentService = new \App\Services\RepaymentService;
                    $repaymentService->confirmRepaymentPayment($repayment, $transaction, [
                        'payment_method' => 'sandbox_bypass',
                    ]);
                }
            }

            if ($transaction->type === 'echeance_repayment' && isset($transaction->metadata['echeance_id'])) {
                $echeance = \App\Models\Echeance::find($transaction->metadata['echeance_id']);
                if ($echeance) {
                    $echeanceService = new \App\Services\EcheanceService;
                    $echeanceService->enregistrerPaiement(
                        $echeance,
                        (float) $transaction->amount,
                        'MMoney - '.$phone,
                        'BYPASS-'.str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT)
                    );

                    $montant = number_format((float) $transaction->amount, 0, ',', ' ');
                    $projetTitre = $echeance->project?->titre ?? 'Projet';

                    \App\Models\UserNotification::create([
                        'user_id' => $echeance->project->user_id,
                        'type' => 'remboursement_effectue',
                        'title' => 'Paiement d\'échéance confirmé',
                        'content' => 'Votre paiement de '.$montant.' FCFA pour l\'échéance #'.
                            $echeance->numero_echeance.' du projet "'.$projetTitre.'" a été confirmé.',
                        'is_read' => false,
                    ]);

                    if ($echeance->funding?->institution?->user_id) {
                        \App\Models\UserNotification::create([
                            'user_id' => $echeance->funding->institution->user_id,
                            'type' => 'remboursement_recu',
                            'title' => 'Remboursement reçu',
                            'content' => 'Un remboursement de '.$montant.
                                ' FCFA a été reçu pour le projet "'.$projetTitre.'".',
                            'is_read' => false,
                        ]);
                    }
                }
            }

            if ($transaction->type === 'disbursement' && isset($transaction->metadata['financement_id'])) {
                $funding = \App\Models\Funding::find($transaction->metadata['financement_id']);
                if ($funding && $funding->statut === \App\Enums\FundingStatus::APPROVED->value) {
                    $workflowService = app(\App\Services\FinancingWorkflowService::class);
                    $funding = $workflowService->finaliserDecaissement($funding, $transaction->user ?? $request->user());
                } elseif ($funding && $funding->statut === \App\Enums\FundingStatus::DISBURSED->value) {
                    Log::info('Disbursement bypass: funding already disbursed', [
                        'funding_id' => $funding->id,
                        'transaction_id' => $transaction->id,
                    ]);
                }
            }

            Log::info('FedaPay bypass: transaction completed', [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'phone' => $phone,
            ]);

            $returnUrl = match ($transaction->type) {
                'echeance_repayment' => '/frontend/dashboard02/porteur/echances.php',
                'repayment' => '/frontend/dashboard02/porteur/remboursement.php',
                'disbursement' => '/frontend/dashboard02/institution/financement.php',
                default => '/frontend/dashboard02/porteur',
            };

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement de '.number_format($transaction->amount, 0, ',', ' ').' XOF confirmé !',
                    'redirect_url' => $returnUrl,
                ]);
            }

            return redirect($returnUrl)
                ->with('success', 'Paiement de '.number_format($transaction->amount, 0, ',', ' ').' XOF confirmé !');
        }

        return view('payment.bypass-confirm', [
            'transaction' => $transaction,
        ]);
    }

    public function bypassCancel(Request $request, \App\Models\Transaction $transaction)
    {
        $transaction->update(['status' => 'cancelled']);

        if ($transaction->type === 'echeance_repayment' && isset($transaction->metadata['echeance_id'])) {
            $echeance = \App\Models\Echeance::find($transaction->metadata['echeance_id']);
            if ($echeance) {
                $echeance->update(['statut' => \App\Enums\EcheanceStatus::CANCELLED->value]);
            }
        }

        if ($transaction->type === 'disbursement' && isset($transaction->metadata['financement_id'])) {
            $funding = \App\Models\Funding::find($transaction->metadata['financement_id']);
            if ($funding && $funding->statut === \App\Enums\FundingStatus::APPROVED->value) {
                $funding->update(['statut' => \App\Enums\FundingStatus::AWAITING_IMF_VALIDATION->value]);
            }
        }

        Log::info('FedaPay bypass: transaction cancelled', [
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
        ]);

        $redirectUrl = match ($transaction->type) {
            'disbursement' => '/frontend/dashboard02/institution/financement.php',
            default => '/frontend/dashboard02/porteur/echances.php',
        };

        return redirect($redirectUrl)
            ->with('error', 'Paiement annulé.');
    }

    /**
     * Vérifier le statut d'une transaction (API)
     */
    public function checkStatus($transactionId)
    {
        try {
            $status = $this->fedaPay->checkTransactionStatus((string) $transactionId);

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
