<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Repayment;
use Illuminate\Http\Request;

class AdminRepaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Repayment::with(['project.owner']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('project', fn ($q) => $q->where('titre', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $stats = [
            'total_repaid' => (clone $query)->where('statut', 'paye')->sum('montant_rembourse'),
            'recovery_rate' => 0,
            'in_progress' => (clone $query)->where('statut', 'en_attente')->count(),
            'defaults' => (clone $query)->where('statut', 'defaulted')->count(),
        ];

        $totalFinance = (clone $query)->sum('montant_total');
        $totalRepaid = (clone $query)->sum('montant_rembourse');
        $stats['recovery_rate'] = $totalFinance > 0 ? round(($totalRepaid / $totalFinance) * 100, 2) : 0;

        $repayments = $query->orderBy('created_at', 'desc')->paginate(15);

        $data = $repayments->map(fn ($r) => [
            'id' => $r->id,
            'project_title' => $r->project?->titre ?? '',
            'porteur_name' => $r->project?->owner?->name ?? '',
            'montant_total' => $r->montant_total,
            'montant_paye' => $r->montant_rembourse,
            'montant_restant' => $r->montant_restant,
            'statut' => $r->statut,
        ]);

        return response()->json([
            'success' => true,
            'total' => $repayments->total(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function projects(Request $request)
    {
        $query = Project::with('owner')->whereHas('repayments', fn ($q) => $q->where('statut', 'paye'));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('titre', 'like', "%{$s}%")
                    ->orWhereHas('owner', fn ($oq) => $oq->where('name', 'like', "%{$s}%"));
            });
        }

        $projects = $query->latest()->paginate(15);

        $data = $projects->map(function ($p) {
            $repayments = $p->repayments->where('statut', 'paye');

            return [
                'id' => $p->id,
                'titre' => $p->titre,
                'porteur_name' => $p->owner?->name ?? '',
                'repayments_count' => $repayments->count(),
                'total_due' => $repayments->sum('montant_total'),
                'total_paid' => $repayments->sum('montant_rembourse'),
                'total_remaining' => $repayments->sum('montant_restant'),
            ];
        });

        $allPaid = Repayment::where('statut', 'paye');
        $totalDue = (clone $allPaid)->sum('montant_total');
        $totalRepaid = (clone $allPaid)->sum('montant_rembourse');
        $stats = [
            'total_projects' => $projects->total(),
            'total_repayments' => (clone $allPaid)->count(),
            'total_repaid' => $totalRepaid,
            'recovery_rate' => $totalDue > 0 ? round(($totalRepaid / $totalDue) * 100, 1) : 0,
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function projectRepayments(int $projectId)
    {
        $project = Project::with('owner')->findOrFail($projectId);
        $repayments = $project->repayments()
            ->where('statut', 'paye')
            ->orderBy('date_echeance', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'titre' => $project->titre,
                'porteur_name' => $project->owner?->name ?? '',
            ],
            'data' => $repayments->map(fn ($r) => [
                'id' => $r->id,
                'montant_total' => $r->montant_total,
                'montant_rembourse' => $r->montant_rembourse,
                'montant_restant' => $r->montant_restant,
                'date_echeance' => $r->date_echeance,
                'statut' => $r->statut,
            ]),
        ]);
    }
}
