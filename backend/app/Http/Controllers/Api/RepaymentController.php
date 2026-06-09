<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Events\RepaymentRecorded;
use App\Http\Controllers\Controller;
use App\Models\Funding;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\RepaymentConfirmation;
use App\Services\ProjectWorkflowService;
use App\Services\RepaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RepaymentController extends Controller
{
    public function __construct(
        protected ProjectWorkflowService $workflow,
        protected RepaymentService $repaymentService
    ) {}

    /**
     * Enregistrer un remboursement (institution)
     */
    public function store(Request $request, Project $project)
    {
        if ($request->user()->role !== 'institution') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution introuvable pour cet utilisateur.'], 422);
        }

        $data = $request->validate([
            'montant_total' => 'required|numeric|min:0',
            'date_echeance' => 'nullable|date',
            'date_paiement' => 'nullable|date',
            'statut' => 'nullable|string|in:paye,en_attente,en_retard',
        ]);

        if (! Funding::where('project_id', $project->id)->where('institution_id', $institution->id)->exists()) {
            return response()->json(['message' => 'Cette institution ne finance pas ce projet.'], 403);
        }

        try {
            $repayment = DB::transaction(function () use ($request, $project, $institution, $data) {
                $status = $project->statusEnum();
                if ($status === ProjectStatus::FUNDED) {
                    $this->workflow->activate($project, $request->user()->id);
                } elseif (! in_array($status, [ProjectStatus::ACTIVE, ProjectStatus::COMPLETED], true)) {
                    throw new RuntimeException('Le projet doit être financé avant enregistrement des remboursements.');
                }

                $repayment = Repayment::create([
                    'project_id' => $project->id,
                    'institution_id' => $institution->id,
                    'montant_total' => $data['montant_total'],
                    'montant_restant' => $data['montant_total'],
                    'date_echeance' => $data['date_echeance'] ?? null,
                    'date_paiement' => $data['date_paiement'] ?? now(),
                    'statut' => $data['statut'] ?? 'paye',
                ]);

                RepaymentConfirmation::create([
                    'remboursement_id' => $repayment->id,
                    'institution_id' => $institution->id,
                    'date_confirmation' => now(),
                ]);

                $this->repaymentService->logHistory($repayment, 'created', $request->user()->id, [
                    'montant' => $data['montant_total'],
                    'statut' => $repayment->statut,
                ]);

                $targetAmount = max((float) $project->montant_finance, (float) $project->montant_demande);
                $paidAmount = (float) Repayment::where('project_id', $project->id)
                    ->where('statut', 'paye')
                    ->sum('montant_total');

                if ($targetAmount > 0 && $paidAmount >= $targetAmount && $project->fresh()->statusEnum() === ProjectStatus::ACTIVE) {
                    $this->workflow->complete($project->fresh(), $request->user()->id);
                }

                return $repayment;
            });
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        event(new RepaymentRecorded($repayment));

        return response()->json($repayment->fresh(), 201);
    }

    /**
     * Lister les remboursements du porteur connecté
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->role !== 'porteur') {
            return response()->json(['message' => 'Accès réservé aux porteurs'], 403);
        }

        $validated = $request->validate([
            'statut' => 'nullable|string|in:paye,en_attente,en_retard',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $result = $this->repaymentService->getPorteurRepayments($user, $validated);

        return response()->json([
            'repayments' => $result['repayments']->load(['project', 'institution']),
            'stats' => $result['stats'],
        ]);
    }

    /**
     * Afficher un remboursement spécifique
     */
    public function show(Request $request, Repayment $repayment)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Vérifier ownership
        if ($repayment->project->user_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $repayment->load(['project', 'institution', 'confirmations']);
        $history = $this->repaymentService->getHistory($repayment);

        return response()->json([
            'repayment' => $repayment,
            'history' => $history,
            'penalty' => $this->repaymentService->calculatePenalty($repayment),
        ]);
    }

    /**
     * Initier un paiement FedaPay pour un remboursement
     */
    public function initiatePayment(Request $request, Repayment $repayment)
    {
        $user = $request->user();

        if (! $user || $user->role !== 'porteur') {
            return response()->json(['message' => 'Accès réservé aux porteurs'], 403);
        }

        // Vérifier ownership
        if ($repayment->project->user_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Vérifier que le remboursement n'est pas déjà payé
        if ($repayment->statut === 'paye') {
            return response()->json(['message' => 'Ce remboursement est déjà payé'], 400);
        }

        try {
            $result = $this->repaymentService->initiateRepaymentPayment($repayment, $user);

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'transaction_id' => $result['transaction_id'],
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Historique des remboursements du porteur
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->role !== 'porteur') {
            return response()->json(['message' => 'Accès réservé aux porteurs'], 403);
        }

        $repayments = Repayment::with('project')
            ->whereHas('project', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('date_echeance', 'desc')
            ->get();

        $history = [];
        $repaymentService = new RepaymentService;
        foreach ($repayments as $repayment) {
            $history = array_merge($history, $repaymentService->getHistory($repayment));
        }

        // Trier par date décroissante
        usort($history, function ($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        return response()->json([
            'history' => $history,
        ]);
    }
}
