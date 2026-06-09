<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use App\Models\Project;
use Illuminate\Http\Request;

class AdminEcheanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Echeance::with(['project.owner']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('project', fn ($q) => $q->where('titre', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $now = now();
        $stats = [
            'month_due' => (clone $query)->whereMonth('date_echeance', $now->month)->whereYear('date_echeance', $now->year)->sum('montant_total'),
            'on_time' => (clone $query)->where('statut', 'paid')->count(),
            'late' => (clone $query)->where('statut', 'overdue')->count(),
            'amount_due' => (clone $query)->where('statut', '!=', 'paid')->sum('montant_restant'),
        ];

        $echeances = $query->orderBy('created_at', 'desc')->paginate(15);

        $data = $echeances->map(fn ($e) => [
            'id' => $e->id,
            'project_title' => $e->project?->titre ?? 'N/A',
            'porteur_name' => $e->project?->owner?->name ?? 'N/A',
            'montant' => $e->montant_total,
            'date_echeance' => $e->date_echeance,
            'statut' => $e->statut,
        ]);

        return response()->json([
            'success' => true,
            'total' => $echeances->total(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function projects(Request $request)
    {
        $query = Project::with('owner')->whereHas('echeances');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('titre', 'like', "%{$s}%")
                    ->orWhereHas('owner', fn ($oq) => $oq->where('name', 'like', "%{$s}%"));
            });
        }

        $projects = $query->latest()->paginate(15);

        $data = $projects->map(function ($p) {
            $echeances = $p->echeances;

            return [
                'id' => $p->id,
                'titre' => $p->titre,
                'porteur_name' => $p->owner?->name ?? 'N/A',
                'echeances_count' => $echeances->count(),
                'total_due' => $echeances->sum('montant_total'),
                'total_paid' => $echeances->sum('montant_paye'),
                'total_remaining' => $echeances->sum('montant_restant'),
                'overdue_count' => $echeances->where('statut', 'overdue')->count(),
            ];
        });

        $stats = [
            'total_projects' => $projects->total(),
            'total_echeances' => Echeance::count(),
            'total_overdue' => Echeance::where('statut', 'overdue')->count(),
            'total_paid' => Echeance::where('statut', 'paid')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function projectEcheances($projectId)
    {
        $project = Project::with('owner')->findOrFail($projectId);
        $echeances = $project->echeances()->with('institution')->orderBy('date_echeance', 'asc')->get();

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'titre' => $project->titre,
                'porteur_name' => $project->owner?->name ?? 'N/A',
            ],
            'data' => $echeances->map(fn ($e) => [
                'id' => $e->id,
                'montant_total' => $e->montant_total,
                'montant_paye' => $e->montant_paye,
                'montant_restant' => $e->montant_restant,
                'date_echeance' => $e->date_echeance,
                'statut' => $e->statut,
                'institution_nom' => $e->institution?->nom ?? 'N/A',
            ]),
        ]);
    }
}
