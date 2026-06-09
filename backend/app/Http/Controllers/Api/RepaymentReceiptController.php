<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Funding;
use App\Models\Repayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RepaymentReceiptController extends Controller
{
    public function generateReceipt($repaymentId)
    {
        $repayment = Repayment::with(['project.owner'])->findOrFail($repaymentId);

        $receiptNumber = 'REC-'.str_pad((string) $repayment->id, 6, '0', STR_PAD_LEFT).'-'.$repayment->created_at?->format('Ymd');

        $data = [
            'receipt_number' => $receiptNumber,
            'date' => now()->format('d/m/Y H:i'),
            'project' => [
                'title' => $repayment->project?->titre ?? 'N/A',
                'code' => 'PRJ-'.str_pad((string) $repayment->project_id, 3, '0', STR_PAD_LEFT),
            ],
            'porteur' => [
                'name' => $repayment->project?->owner?->name ?? 'N/A',
                'email' => $repayment->project?->owner?->email ?? 'N/A',
            ],
            'repayment' => [
                'montant_total' => (float) $repayment->montant_total,
                'montant_rembourse' => (float) $repayment->montant_rembourse,
                'montant_restant' => (float) ($repayment->montant_restant ?? $repayment->montant_total - $repayment->montant_rembourse),
                'date_echeance' => $repayment->date_echeance?->format('d/m/Y') ?? 'N/A',
                'date_paiement' => $repayment->date_paiement?->format('d/m/Y') ?? 'N/A',
                'statut' => $repayment->statut,
                'statut_label' => $this->statusLabel($repayment->statut),
            ],
            'generated_by' => auth()->user()?->name ?? 'Système',
        ];

        $this->logAccess($repayment);

        return response()->json([
            'success' => true,
            'receipt' => $data,
        ]);
    }

    public function simulateRepayment(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'taux_interet' => 'required|numeric|min:0|max:100',
            'duree_mois' => 'required|integer|min:1|max:360',
            'date_debut' => 'required|date',
        ]);

        $montant = (float) $request->montant;
        $taux = (float) $request->taux_interet;
        $duree = (int) $request->duree_mois;
        $dateDebut = Carbon::parse($request->date_debut);

        $tauxMensuel = $taux / 100 / 12;
        $mensualite = $this->calculateMonthlyPayment($montant, $tauxMensuel, $duree);
        $totalRembourse = $mensualite * $duree;
        $totalInterets = $totalRembourse - $montant;

        $echeancier = [];
        $soldeRestant = $montant;

        for ($i = 1; $i <= $duree; $i++) {
            $interetMois = $soldeRestant * $tauxMensuel;
            $capitalMois = $mensualite - $interetMois;
            $soldeRestant -= $capitalMois;

            if ($soldeRestant < 0) {
                $capitalMois += $soldeRestant;
                $soldeRestant = 0;
            }

            $echeancier[] = [
                'numero' => $i,
                'date' => (clone $dateDebut)->addMonths($i)->format('d/m/Y'),
                'montant' => round($mensualite, 0),
                'capital' => round($capitalMois, 0),
                'interet' => round($interetMois, 0),
                'solde_restant' => round(max($soldeRestant, 0), 0),
            ];

            if ($soldeRestant <= 0) {
                break;
            }
        }

        return response()->json([
            'success' => true,
            'simulation' => [
                'montant' => $montant,
                'taux_annuel' => $taux,
                'duree_mois' => $duree,
                'mensualite' => round($mensualite, 0),
                'total_rembourse' => round($totalRembourse, 0),
                'total_interets' => round($totalInterets, 0),
                'taeg' => round($this->calculateTaeg($montant, $mensualite, $duree), 2),
                'echeancier' => $echeancier,
            ],
        ]);
    }

    public function porteurLateRepayments()
    {
        $user = auth()->user();

        $projectIds = $user->projects()->pluck('id');

        $repayments = Repayment::with('project')
            ->whereIn('project_id', $projectIds)
            ->where('statut', 'en_retard')
            ->orderBy('date_echeance')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'project_title' => $r->project?->titre ?? 'N/A',
                'montant_due' => (float) $r->montant_total,
                'montant_restant' => (float) ($r->montant_restant ?? $r->montant_total),
                'date_echeance' => $r->date_echeance?->format('d/m/Y'),
                'jours_retard' => $r->date_echeance ? now()->diffInDays($r->date_echeance, false) : 0,
            ]);

        return response()->json([
            'success' => true,
            'count' => $repayments->count(),
            'total_due' => $repayments->sum('montant_due'),
            'data' => $repayments,
        ]);
    }

    public function institutionLateRepayments()
    {
        $institution = auth()->user()->institution;
        if (! $institution) {
            return response()->json(['success' => false, 'message' => 'Institution non trouvée'], 404);
        }

        $fundedProjectIds = Funding::where('institution_id', $institution->id)->pluck('project_id');

        $repayments = Repayment::with('project.owner')
            ->whereIn('project_id', $fundedProjectIds)
            ->where('statut', 'en_retard')
            ->orderBy('date_echeance')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'project_title' => $r->project?->titre ?? 'N/A',
                'porteur_name' => $r->project?->owner?->name ?? 'N/A',
                'montant_due' => (float) $r->montant_total,
                'montant_restant' => (float) ($r->montant_restant ?? $r->montant_total),
                'date_echeance' => $r->date_echeance?->format('d/m/Y'),
                'jours_retard' => $r->date_echeance ? now()->diffInDays($r->date_echeance, false) : 0,
            ]);

        return response()->json([
            'success' => true,
            'count' => $repayments->count(),
            'total_due' => $repayments->sum('montant_due'),
            'data' => $repayments,
        ]);
    }

    private function calculateMonthlyPayment(float $principal, float $monthlyRate, int $months): float
    {
        if ($monthlyRate <= 0) {
            return $principal / $months;
        }

        $factor = pow(1 + $monthlyRate, $months);

        return $principal * $monthlyRate * $factor / ($factor - 1);
    }

    private function calculateTaeg(float $montant, float $mensualite, int $duree): float
    {
        if ($montant <= 0 || $mensualite <= 0 || $duree <= 0) {
            return 0;
        }

        $totalRembourse = $mensualite * $duree;
        $interets = $totalRembourse - $montant;

        return ($interets / $montant) / ($duree / 12) * 100;
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'paye' => 'Payé',
            'en_attente' => 'En attente',
            'en_retard' => 'En retard',
            'partiel' => 'Partiel',
            default => $status ?? 'Inconnu',
        };
    }

    private function logAccess(Repayment $repayment): void
    {
        AuditLog::log(
            auth()->id(),
            'Reçu de remboursement généré: #'.$repayment->id.' ('.($repayment->project?->titre ?? 'N/A').')',
            'bi-receipt',
            'info',
            'info'
        );
    }
}
