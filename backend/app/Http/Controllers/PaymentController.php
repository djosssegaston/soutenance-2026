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
        return view('payment.failed', [
            'message' => $request->get('message', 'Le paiement a échoué.'),
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
