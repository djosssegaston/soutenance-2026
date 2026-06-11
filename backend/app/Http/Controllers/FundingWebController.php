<?php

namespace App\Http\Controllers;

use App\Enums\FundingStatus;
use App\Models\Funding;
use App\Services\FundingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundingWebController extends Controller
{
    public function __construct(
        protected FundingService $fundingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $fundings = $this->fundingService->getPorteurFundings($user);
        $statusLabels = $this->fundingService->getFundingStatusLabels();

        $data = $fundings->map(function (Funding $f) use ($statusLabels) {
            $statut = $statusLabels[$f->statut] ?? ['label' => $f->statut, 'color' => 'secondary'];

            return [
                'id' => $f->id,
                'project_id' => $f->project_id,
                'project_titre' => $f->project->titre ?? '',
                'project_statut' => $f->project->statut ?? '',
                'institution_id' => $f->institution_id,
                'institution_nom' => $f->institution->nom ?? '',
                'institution_logo' => $f->institution->logo ?? null,
                'montant' => (float) ($f->montant_propose ?: $f->montant),
                'montant_propose' => (float) $f->montant_propose,
                'taux_interet' => (float) $f->taux_interet,
                'duree' => $f->duree,
                'date_financement' => $f->date_financement?->format('d M Y') ?? '',
                'statut' => $statut['label'],
                'statut_color' => $statut['color'],
                'statut_raw' => $f->statut,
            ];
        });

        return response()->json($data);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = $this->fundingService->getPorteurFundingStats($user);

        return response()->json($stats);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $history = $this->fundingService->getPorteurFundingHistory($user);

        $meta = $this->fundingService->getHistoryStatusMeta('funded');

        $data = $history->map(function ($item) {
            if ($item['type'] === 'statut') {
                $statusMeta = $this->fundingService->getHistoryStatusMeta($item['nouveau_statut']);

                return [
                    'type' => 'statut',
                    'icon' => $statusMeta['icon'],
                    'color' => $statusMeta['color'],
                    'action' => $statusMeta['label'],
                    'project_titre' => $item['project_titre'],
                    'detail' => $item['raison'] ? 'Raison : '.$item['raison'] : '',
                    'acteur' => $item['acteur'],
                    'date' => $item['date'],
                ];
            }

            return [
                'type' => 'transaction',
                'icon' => 'bi bi-credit-card',
                'color' => 'success',
                'action' => 'Paiement reçu',
                'project_titre' => $item['project_titre'],
                'detail' => number_format($item['montant'], 0, ',', ' ').' FCFA via '.$item['methode'],
                'acteur' => 'Système',
                'date' => $item['date'],
            ];
        });

        return response()->json($data);
    }

    public function documents(Request $request): JsonResponse
    {
        $user = $request->user();
        $documents = $this->fundingService->getFundingDocuments($user);

        $data = $documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'project_titre' => $doc->project->titre ?? '',
                'type' => $doc->type ?? 'document',
                'nom' => $doc->nom ?? $doc->fichier_original ?? 'Document',
                'fichier' => $doc->fichier ?? $doc->path ?? null,
                'date' => $doc->created_at->format('d M Y'),
                'download_url' => $doc->id ? route('secure.documents.project.download', ['document' => $doc->id]) : null,
                'view_url' => $doc->id ? route('documents.secure.project.view', ['document' => $doc->id]) : null,
            ];
        });

        return response()->json($data);
    }

    public function accept(Request $request, Funding $funding): JsonResponse
    {
        $user = $request->user();

        if ($funding->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        if ($funding->statut !== FundingStatus::AWAITING_BORROWER_PLAN->value) {
            return response()->json(['message' => 'Cette proposition ne peut plus être acceptée.'], 422);
        }

        $funding->update([
            'statut' => FundingStatus::AWAITING_IMF_VALIDATION->value,
            'montant_valide' => $funding->montant_propose,
            'date_validation' => now(),
        ]);

        $this->logAction($funding->id, 'confirmation_porteur', $user->name, 'Proposition de financement acceptée par le porteur.');

        return response()->json(['message' => 'Proposition acceptée. En attente de validation par l\'institution.', 'funding' => $funding]);
    }

    public function refuse(Request $request, Funding $funding): JsonResponse
    {
        $user = $request->user();

        if ($funding->project->user_id !== $user->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        if ($funding->statut !== FundingStatus::AWAITING_BORROWER_PLAN->value) {
            return response()->json(['message' => 'Cette proposition ne peut plus être refusée.'], 422);
        }

        $funding->update([
            'statut' => FundingStatus::REJECTED->value,
        ]);

        $this->logAction($funding->id, 'refus_porteur', $user->name, 'Proposition de financement refusée par le porteur.');

        return response()->json(['message' => 'Proposition refusée.', 'funding' => $funding]);
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
            \Illuminate\Support\Facades\Log::warning('Erreur historisation financement', ['error' => $e->getMessage()]);
        }
    }
}
