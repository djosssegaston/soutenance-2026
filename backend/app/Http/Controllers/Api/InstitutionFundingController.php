<?php

namespace App\Http\Controllers\Api;

use App\Enums\FundingStatus;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Transaction;
use App\Services\EcheanceService;
use App\Services\FedaPayService;
use App\Services\FinancingWorkflowService;
use App\Services\FundingManagementService;
use Illuminate\Http\Request;

class InstitutionFundingController extends Controller
{
    protected FundingManagementService $fundingService;

    protected FinancingWorkflowService $workflow;

    protected EcheanceService $echeanceService;

    protected FedaPayService $fedaPayService;

    public function __construct(
        FundingManagementService $fundingService,
        FinancingWorkflowService $workflow,
        EcheanceService $echeanceService,
        FedaPayService $fedaPayService,
    ) {
        $this->fundingService = $fundingService;
        $this->workflow = $workflow;
        $this->echeanceService = $echeanceService;
        $this->fedaPayService = $fedaPayService;
    }

    private function getInstitution(Request $request): ?Institution
    {
        return $request->user()->institution;
    }

    private function institutionNotFound(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Profil institution non trouvé.'], 404);
    }

    public function index(Request $request)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return response()->json(['data' => [], 'links' => [], 'meta' => ['current_page' => 1, 'last_page' => 1, 'total' => 0]]);
        }

        $query = Funding::where('institution_id', $institution->id)
            ->with(['project.owner', 'porteur', 'echeances']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $fundings = $query->latest()->paginate(10);

        return response()->json($fundings);
    }

    public function stats(Request $request)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return response()->json([
                'total_finance' => 0,
                'actifs' => 0,
                'en_attente' => 0,
                'en_validation' => 0,
                'confirmes' => 0,
                'termines' => 0,
                'refuses' => 0,
                'roi_estime' => 0,
            ]);
        }

        $institutionId = $institution->id;

        $stats = [
            'total_finance' => Funding::where('institution_id', $institutionId)->whereIn('statut', [
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
                FundingStatus::COMPLETED->value,
            ])->sum('montant_valide'),
            'actifs' => Funding::where('institution_id', $institutionId)->whereIn('statut', [
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
            ])->count(),
            'en_attente' => Funding::where('institution_id', $institutionId)->where('statut', FundingStatus::AWAITING_BORROWER_PLAN->value)->count(),
            'en_validation' => Funding::where('institution_id', $institutionId)->where('statut', FundingStatus::AWAITING_IMF_VALIDATION->value)->count(),
            'confirmes' => Funding::where('institution_id', $institutionId)->where('statut', FundingStatus::APPROVED->value)->count(),
            'termines' => Funding::where('institution_id', $institutionId)->whereIn('statut', [
                FundingStatus::COMPLETED->value,
                FundingStatus::DEFAULTED->value,
            ])->count(),
            'refuses' => Funding::where('institution_id', $institutionId)->where('statut', FundingStatus::REJECTED->value)->count(),
            'roi_estime' => (float) (Funding::where('institution_id', $institutionId)->whereIn('statut', [
                FundingStatus::DISBURSED->value,
                FundingStatus::ACTIVE->value,
                FundingStatus::COMPLETED->value,
            ])->avg('taux_interet') ?? 0),
        ];

        return response()->json($stats);
    }

    public function store(Request $request)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'montant_propose' => 'required|numeric|min:1',
            'taux_interet' => 'required|numeric|min:0|max:100',
            'duree' => 'required|integer|min:1|max:360',
            'conditions' => 'nullable|string',
            'frais' => 'nullable|string',
            'commentaires' => 'nullable|string',
            'confirm_overfunding' => 'nullable|in:0,1',
        ]);

        $project = Project::findOrFail($data['project_id']);

        $overfundingDetected = $this->fundingService->checkOverfunding(
            $project->id,
            (float) $data['montant_propose']
        );

        if ($overfundingDetected) {
            $confirmOverfunding = $data['confirm_overfunding'] ?? null;

            if ($confirmOverfunding === null) {
                // Première soumission : retourner un avertissement
                $currentTotal = (float) $project->financements()
                    ->whereIn('statut', [
                        FundingStatus::PROPOSED->value,
                        FundingStatus::AWAITING_BORROWER_PLAN->value,
                        FundingStatus::AWAITING_IMF_VALIDATION->value,
                        FundingStatus::APPROVED->value,
                        FundingStatus::DISBURSED->value,
                        FundingStatus::ACTIVE->value,
                    ])
                    ->sum('montant_propose');

                return response()->json([
                    'overfunding_warning' => true,
                    'message' => 'Le montant total des financements dépasserait le montant demandé pour ce projet.',
                    'existing_total' => $currentTotal,
                    'proposed_amount' => (float) $data['montant_propose'],
                    'max_allowed' => (float) $project->montant_demande,
                ], 409);
            }

            if ($confirmOverfunding === '0') {
                // L'IMF refuse le surfinancement → projet reprend "En cours d'analyse"
                $project->update(['statut' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value]);

                return response()->json([
                    'message' => 'Proposition de financement annulée. Le projet reprend le statut "En cours d\'analyse".',
                ]);
            }
            // confirm_overfunding === '1' → continuer (surcharge acceptée)
        }

        if (! $project->peutEtreFinance()) {
            $statusLabel = $project->statusEnum()->label();

            return response()->json([
                'message' => "Ce projet ne peut pas recevoir de nouvelle proposition de financement. Statut actuel du projet : {$statusLabel}.",
            ], 422);
        }

        try {
            $funding = $this->workflow->imfFaireProposition($request->user(), $project, $data);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Proposition de financement envoyée au porteur.',
            'funding' => $funding,
        ], 201);
    }

    public function approvePlan(Request $request, $id)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $funding = Funding::where('institution_id', $institution->id)->findOrFail($id);

        try {
            $bypassActive = $this->fedaPayService->isBypassActive();

            $funding = $this->workflow->imfApprouverPlan(
                $request->user(),
                $funding,
                skipDisbursement: $bypassActive
            );

            if ($bypassActive) {
                // Créer une transaction de type disbursement pour le paiement IMF
                $callbackUrl = url('/frontend/dashboard02/institution/financement.php').'?payment_status=pending';
                $transaction = Transaction::create([
                    'project_id' => $funding->project_id,
                    'user_id' => $request->user()->id,
                    'institution_id' => $institution->id,
                    'type' => 'disbursement',
                    'amount' => (float) $funding->montant_propose,
                    'currency' => 'XOF',
                    'status' => 'pending',
                    'metadata' => [
                        'financement_id' => $funding->id,
                        'project_title' => $funding->project->titre,
                        'institution_name' => $institution->nom,
                        'initiated_at' => now()->toIso8601String(),
                        'sandbox_bypass' => true,
                    ],
                ]);

                $paymentUrl = $this->fedaPayService->bypassPaymentUrl($transaction, $callbackUrl);

                return response()->json([
                    'message' => 'Plan approuvé. Veuillez procéder au paiement pour finaliser le décaissement.',
                    'funding' => $funding,
                    'payment_url' => $paymentUrl,
                    'transaction_id' => $transaction->id,
                    'amount' => (float) $funding->montant_propose,
                ]);
            }

            return response()->json([
                'message' => 'Plan approuvé, financement décaissé et échéancier généré.',
                'funding' => $funding->load(['echeances']),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function rejectPlan(Request $request, $id)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $request->validate(['motif' => 'required|string']);

        $funding = Funding::where('institution_id', $institution->id)->findOrFail($id);

        try {
            $funding = $this->workflow->imfRejeterPlan($request->user(), $funding, $request->motif);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Plan de remboursement rejeté.',
            'funding' => $funding,
        ]);
    }

    public function requestRevision(Request $request, $id)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $request->validate(['commentaire' => 'required|string']);

        $funding = Funding::where('institution_id', $institution->id)->findOrFail($id);

        try {
            $funding = $this->workflow->imfDemanderRevision($request->user(), $funding, $request->commentaire);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Révision demandée au porteur.',
            'funding' => $funding,
        ]);
    }

    public function echeances(Request $request, $id)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $funding = Funding::where('institution_id', $institution->id)->findOrFail($id);

        $this->echeanceService->mettreAJourStatutsEcheances($funding);

        $echeances = $funding->echeances()->orderBy('numero_echeance')->get();

        $progression = $this->echeanceService->getProgression($funding);

        return response()->json([
            'funding' => $funding,
            'echeances' => $echeances,
            'progression' => $progression,
        ]);
    }

    public function decaisser(Request $request, $id)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $funding = Funding::where('institution_id', $institution->id)->findOrFail($id);

        // Si bypass actif et funding approuvé, initier un paiement bypass
        if ($this->fedaPayService->isBypassActive() && $funding->statut === FundingStatus::APPROVED->value) {
            $callbackUrl = url('/frontend/dashboard02/institution/financement.php').'?payment_status=pending';
            $transaction = Transaction::create([
                'project_id' => $funding->project_id,
                'user_id' => $request->user()->id,
                'institution_id' => $institution->id,
                'type' => 'disbursement',
                'amount' => (float) $funding->montant_propose,
                'currency' => 'XOF',
                'status' => 'pending',
                'metadata' => [
                    'financement_id' => $funding->id,
                    'project_title' => $funding->project->titre,
                    'institution_name' => $institution->nom,
                    'initiated_at' => now()->toIso8601String(),
                    'sandbox_bypass' => true,
                ],
            ]);

            $paymentUrl = $this->fedaPayService->bypassPaymentUrl($transaction, $callbackUrl);

            return response()->json([
                'message' => 'Veuillez procéder au paiement pour finaliser le décaissement.',
                'payment_url' => $paymentUrl,
                'transaction_id' => $transaction->id,
                'amount' => (float) $funding->montant_propose,
            ]);
        }

        if ($funding->statut !== FundingStatus::APPROVED->value) {
            return response()->json(['message' => 'Le financement doit être approuvé avant décaissement.'], 422);
        }

        $funding->update([
            'statut' => FundingStatus::DISBURSED->value,
            'montant_decaisse' => $funding->montant_valide ?: $funding->montant_propose,
            'date_decaissement' => now(),
        ]);

        $project = $funding->project;
        $project->increment('montant_finance', $funding->montant_decaisse);

        $this->echeanceService->genererEcheancier($funding);

        $this->fundingService->logAction($funding->id, 'decaissement', $request->user()->name, 'Fonds décaissés et échéancier généré.');

        return response()->json([
            'message' => 'Décaissement validé et échéancier généré.',
            'funding' => $funding->fresh()->load('echeances'),
        ]);
    }

    public function show(Request $request, $id)
    {
        $institution = $this->getInstitution($request);
        if (! $institution) {
            return $this->institutionNotFound();
        }

        $funding = Funding::with([
            'project.owner', 'project.documents', 'porteur',
            'histories', 'documents', 'echeances',
        ])
            ->where('institution_id', $institution->id)
            ->findOrFail($id);

        $progression = $this->echeanceService->getProgression($funding);

        return response()->json([
            'funding' => $funding,
            'progression' => $progression,
        ]);
    }
}
