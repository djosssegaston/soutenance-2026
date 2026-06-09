<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use App\Models\Funding;
use App\Models\UserNotification;
use App\Services\EcheanceService;
use App\Services\FedaPayService;
use App\Services\FinancingWorkflowService;
use Illuminate\Http\Request;

class PorteurFinancingController extends Controller
{
    public function __construct(
        protected FinancingWorkflowService $workflow,
        protected EcheanceService $echeanceService,
        protected FedaPayService $fedaPayService,
    ) {}

    public function propositions(Request $request)
    {
        $user = $request->user();
        $projectIds = $user->projects()->pluck('id');

        $propositions = Funding::with(['institution', 'project'])
            ->whereIn('project_id', $projectIds)
            ->where('statut', 'awaiting_borrower_plan')
            ->latest()
            ->get();

        return response()->json([
            'propositions' => $propositions,
        ]);
    }

    public function soumettrePlan(Request $request, Funding $funding)
    {
        $user = $request->user();

        if ($funding->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $data = $request->validate([
            'montant_mensuel' => 'required|numeric|min:1',
            'jour_remboursement' => 'required|integer|min:1|max:31',
            'commentaire' => 'nullable|string',
        ]);

        try {
            $funding = $this->workflow->porteurSoumettrePlan($user, $funding, $data);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Plan de remboursement soumis à l\'institution.',
            'funding' => $funding,
        ]);
    }

    public function mesFinancements(Request $request)
    {
        $user = $request->user();
        $projectIds = $user->projects()->pluck('id');

        $fundings = Funding::with(['institution', 'project', 'echeances'])
            ->whereIn('project_id', $projectIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $fundings->each(function ($funding) {
            $this->echeanceService->mettreAJourStatutsEcheances($funding);
        });

        return response()->json([
            'fundings' => $fundings,
        ]);
    }

    public function echeances(Request $request, Funding $funding)
    {
        $user = $request->user();

        if ($funding->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $this->echeanceService->mettreAJourStatutsEcheances($funding);

        $echeances = $funding->echeances()->orderBy('numero_echeance')->get();
        $progression = $this->echeanceService->getProgression($funding);

        return response()->json([
            'funding' => $funding->load('institution'),
            'echeances' => $echeances,
            'progression' => $progression,
        ]);
    }

    public function detailFinancement(Request $request, Funding $funding)
    {
        $user = $request->user();

        if ($funding->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $this->echeanceService->mettreAJourStatutsEcheances($funding);

        $funding->load(['institution', 'project', 'echeances' => function ($q) {
            $q->orderBy('numero_echeance');
        }, 'histories']);

        $progression = $this->echeanceService->getProgression($funding);

        return response()->json([
            'funding' => $funding,
            'progression' => $progression,
            'montant_total_rembourser' => $funding->montant_total_a_rembourser,
            'interets_total' => $funding->interets_total,
            'reste_du' => $funding->reste_du,
            'progression_pourcentage' => $funding->progression_remboursement,
        ]);
    }

    public function effectuerPaiement(Request $request, Echeance $echeance)
    {
        $user = $request->user();

        if ($echeance->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        if ($echeance->isPayee()) {
            return response()->json(['message' => 'Cette échéance est déjà payée.'], 422);
        }

        $montant = (float) ($request->montant ?? $echeance->montant_restant);

        if ($montant <= 0 || $montant > (float) $echeance->montant_restant) {
            return response()->json(['message' => 'Montant invalide.'], 422);
        }

        $methode = $request->methode_paiement ?? 'manuel';
        $reference = $request->transaction_reference;

        $echeance = $this->echeanceService->enregistrerPaiement($echeance, $montant, $methode, $reference);

        $funding = $echeance->funding;
        if ($funding) {
            $termine = $this->workflow->verifierEtCloturer($funding);
        }

        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'remboursement_effectue',
            'title' => 'Remboursement effectué',
            'content' => 'Vous avez remboursé '.number_format($montant, 0, ',', ' ').
                ' FCFA pour l\'échéance #'.$echeance->numero_echeance.
                ' du projet "'.$echeance->project->titre.'".',
            'is_read' => false,
        ]);

        if ($funding && $funding->institution && $funding->institution->user_id) {
            UserNotification::create([
                'user_id' => $funding->institution->user_id,
                'type' => 'remboursement_recu',
                'title' => 'Remboursement reçu',
                'content' => 'Un remboursement de '.number_format($montant, 0, ',', ' ').
                    ' FCFA a été reçu pour le projet "'.$echeance->project->titre.'".',
                'is_read' => false,
            ]);
        }

        return response()->json([
            'message' => 'Paiement enregistré avec succès.',
            'echeance' => $echeance->fresh(),
            'funding_termine' => $termine ?? false,
        ]);
    }

    public function initierPaiementFedaPay(Request $request, Echeance $echeance)
    {
        $user = $request->user();

        if ($echeance->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        if ($echeance->isPayee()) {
            return response()->json(['message' => 'Cette échéance est déjà payée.'], 422);
        }

        $montant = (float) ($request->montant ?? $echeance->montant_restant);

        if ($montant <= 0 || ($montant > (float) $echeance->montant_restant)) {
            return response()->json(['message' => 'Montant invalide.'], 422);
        }

        $customerData = [
            'firstname' => $user->prenom ?? explode(' ', $user->name)[0],
            'lastname' => $user->nom ?? explode(' ', $user->name)[1] ?? 'Client',
            'email' => $user->email,
            'phone' => $user->telephone ?? '22900000000',
        ];

        try {
            $result = $this->fedaPayService->initierPaiementEcheanceViaFedaPay(
                $echeance,
                $customerData,
                $montant
            );

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'transaction_id' => $result['transaction_id'],
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
