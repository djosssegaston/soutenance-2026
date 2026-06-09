<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Funding;
use App\Models\Project;
use App\Models\Repayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFinanceController extends Controller
{
    public function overview()
    {
        $totalFunding = (float) Funding::sum('montant');
        $totalRepaid = (float) Repayment::where('statut', 'paye')->sum('montant_rembourse');
        $totalDue = (float) Repayment::whereIn('statut', ['en_attente', 'en_retard'])->sum('montant_total');
        $totalProjects = Project::count();
        $fundedProjects = Project::whereIn('statut', ['funded', 'active', 'repaid', 'closed', 'completed'])->count();

        $repaymentRate = $totalDue > 0 ? round(($totalRepaid / $totalDue) * 100, 1) : 0;

        $par30 = $this->calculatePar30();

        $monthlyEvolution = Funding::select(
            DB::raw('YEAR(date_financement) as year'),
            DB::raw('MONTH(date_financement) as month'),
            DB::raw('SUM(montant) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereNotNull('date_financement')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($r) => [
                'year' => $r->year,
                'month' => $r->month,
                'total' => (float) $r->total,
                'count' => $r->count,
            ]);

        $repaymentDetail = [
            'total_institutions' => Funding::distinct('institution_id')->count('institution_id'),
            'total_projects_funded' => $fundedProjects,
            'average_per_project' => $fundedProjects > 0 ? round($totalFunding / $fundedProjects, 0) : 0,
            'total_transactions' => Repayment::count(),
            'recovery_rate' => $repaymentRate,
            'par30' => $par30,
        ];

        AuditLog::log(auth()->id(), 'Consultation des données financières', 'bi-currency-exchange', 'info', 'info');

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'total_funding' => $totalFunding,
                    'total_repaid' => $totalRepaid,
                    'total_due' => $totalDue,
                    'outstanding' => $totalDue - $totalRepaid,
                    'total_projects' => $totalProjects,
                    'funded_projects' => $fundedProjects,
                ],
                'rates' => [
                    'repayment_rate' => $repaymentRate,
                    'par30' => $par30,
                ],
                'monthly_evolution' => $monthlyEvolution,
                'repayment_detail' => $repaymentDetail,
            ],
        ]);
    }

    public function repaymentTable(Request $request)
    {
        $query = Repayment::with(['project.owner', 'project.financements.institution']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('project', fn ($q) => $q->where('titre', 'like', "%{$s}%"))
                ->orWhereHas('project.owner', fn ($q) => $q->where('name', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_echeance', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_echeance', '<=', $request->date_to);
        }

        $sortField = $request->sort ?? 'created_at';
        $sortDir = $request->dir ?? 'desc';
        $allowedSorts = ['created_at', 'date_echeance', 'montant_total', 'montant_rembourse', 'statut'];
        if (! in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        $perPage = min((int) ($request->per_page ?? 15), 100);

        $stats = [
            'total_repayments' => (clone $query)->count(),
            'total_paid' => (clone $query)->where('statut', 'paye')->sum('montant_rembourse'),
            'total_pending' => (clone $query)->whereIn('statut', ['en_attente', 'en_retard'])->sum('montant_total'),
            'overdue_count' => (clone $query)->where('statut', 'en_retard')->count(),
        ];

        $repayments = $query->orderBy($sortField, $sortDir)->paginate($perPage);

        $data = $repayments->map(fn ($r) => [
            'id' => $r->id,
            'project_title' => $r->project?->titre ?? 'N/A',
            'porteur_name' => $r->project?->owner?->name ?? 'N/A',
            'institution_name' => $r->project?->financements->first()?->institution?->nom ?? 'N/A',
            'montant_total' => (float) $r->montant_total,
            'montant_paye' => (float) $r->montant_rembourse,
            'montant_restant' => (float) ($r->montant_restant ?? $r->montant_total - $r->montant_rembourse),
            'date_echeance' => $r->date_echeance?->format('d/m/Y'),
            'date_paiement' => $r->date_paiement?->format('d/m/Y'),
            'statut' => $r->statut,
            'statut_label' => $this->repaymentStatusLabel($r->statut),
        ]);

        return response()->json([
            'success' => true,
            'total' => $repayments->total(),
            'per_page' => $repayments->perPage(),
            'current_page' => $repayments->currentPage(),
            'last_page' => $repayments->lastPage(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function fundingTable(Request $request)
    {
        $query = Funding::with(['project.owner', 'institution']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('project', fn ($q) => $q->where('titre', 'like', "%{$s}%"))
                ->orWhereHas('institution', fn ($q) => $q->where('nom', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $sortField = $request->sort ?? 'created_at';
        $sortDir = $request->dir ?? 'desc';
        $allowedSorts = ['created_at', 'montant', 'date_financement', 'statut'];
        if (! in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        $perPage = min((int) ($request->per_page ?? 15), 100);

        $fundings = $query->orderBy($sortField, $sortDir)->paginate($perPage);

        $data = $fundings->map(fn ($f) => [
            'id' => $f->id,
            'reference' => 'FIN-'.str_pad((string) $f->id, 5, '0', STR_PAD_LEFT),
            'project_title' => $f->project?->titre ?? 'N/A',
            'institution_name' => $f->institution?->nom ?? 'N/A',
            'montant' => (float) ($f->montant_valide ?? $f->montant_propose ?? $f->montant ?? 0),
            'type' => $f->type ?? 'loan',
            'statut' => $f->statut,
            'statut_label' => $this->fundingStatusLabel($f->statut),
            'date_financement' => $f->date_financement?->format('d/m/Y'),
            'created_at' => $f->created_at?->format('d/m/Y'),
        ]);

        $stats = [
            'total_volume' => (float) (clone $query)->sum('montant'),
            'active_count' => (clone $query)->whereIn('statut', ['active', 'disbursed', 'approved'])->count(),
            'pending_count' => (clone $query)->whereIn('statut', ['pending', 'awaiting_imf_validation'])->count(),
        ];

        return response()->json([
            'success' => true,
            'total' => $fundings->total(),
            'per_page' => $fundings->perPage(),
            'current_page' => $fundings->currentPage(),
            'last_page' => $fundings->lastPage(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    private function calculatePar30(): float
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $overdueAmount = Repayment::where('statut', 'en_retard')
            ->where('date_echeance', '<=', $thirtyDaysAgo)
            ->sum('montant_restant');
        $totalOutstanding = Repayment::whereIn('statut', ['en_attente', 'en_retard'])
            ->sum('montant_total');

        return $totalOutstanding > 0 ? round(($overdueAmount / $totalOutstanding) * 100, 1) : 0;
    }

    private function repaymentStatusLabel(?string $status): string
    {
        return match ($status) {
            'paye' => 'Payé',
            'en_attente' => 'En attente',
            'en_retard' => 'En retard',
            'partiel' => 'Partiel',
            'litige' => 'Litige',
            'defaulted' => 'Défaut',
            default => $status ?? 'Inconnu',
        };
    }

    private function fundingStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending', 'en_cours_de_traitement' => 'En cours',
            'awaiting_imf_validation' => 'Validation IMF',
            'approved', 'accepte' => 'Approuvé',
            'disbursed', 'finance', 'active' => 'Décaissé',
            'completed', 'termine' => 'Terminé',
            'rejected', 'refuse' => 'Rejeté',
            default => $status ?? 'Inconnu',
        };
    }
}
