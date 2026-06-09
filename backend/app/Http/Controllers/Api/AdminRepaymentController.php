<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'project_title' => $r->project?->titre ?? 'N/A',
            'porteur_name' => $r->project?->owner?->name ?? 'N/A',
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
}
