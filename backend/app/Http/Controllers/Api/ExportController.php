<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Echeance;
use App\Models\Funding;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'projects');
        $filename = match ($type) {
            'projects' => 'projets_'.now()->format('Y-m-d').'.csv',
            'users' => 'utilisateurs_'.now()->format('Y-m-d').'.csv',
            'repayments' => 'remboursements_'.now()->format('Y-m-d').'.csv',
            'fundings' => 'financements_'.now()->format('Y-m-d').'.csv',
            'echeances' => 'echeances_'.now()->format('Y-m-d').'.csv',
            default => 'export_'.now()->format('Y-m-d').'.csv',
        };

        $data = $this->getExportData($type, $request);
        $columns = $data['columns'];
        $rows = $data['rows'];

        $callback = function () use ($columns, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rows as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        AuditLog::log(auth()->id(), "Export CSV: {$type}", 'bi-download', 'info', 'info');

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function exportJson(Request $request)
    {
        $type = $request->query('type', 'projects');
        $data = $this->getExportData($type, $request);

        AuditLog::log(auth()->id(), "Export JSON: {$type}", 'bi-download', 'info', 'info');

        return response()->json([
            'success' => true,
            'export_type' => $type,
            'exported_at' => now()->toIso8601String(),
            'columns' => $data['columns'],
            'data' => $data['rows'],
        ]);
    }

    private function getExportData(string $type, Request $request): array
    {
        return match ($type) {
            'projects' => $this->exportProjects($request),
            'users' => $this->exportUsers($request),
            'repayments' => $this->exportRepayments($request),
            'fundings' => $this->exportFundings($request),
            'echeances' => $this->exportEcheances($request),
            default => ['columns' => [], 'rows' => []],
        };
    }

    private function exportProjects(Request $request): array
    {
        $query = Project::with('owner');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('secteur')) {
            $query->where('secteur', $request->secteur);
        }

        $projects = $query->latest()->get();

        $columns = ['ID', 'Titre', 'Porteur', 'Montant Demandé', 'Secteur', 'Statut', 'Créé le'];

        $rows = $projects->map(fn ($p) => [
            $p->id,
            $p->titre,
            $p->owner?->name ?? '',
            number_format((float) $p->montant_demande, 0, ',', ' ').' FCFA',
            $p->secteur ?? '',
            $p->statut ?? 'draft',
            $p->created_at?->format('d/m/Y') ?? '',
        ])->toArray();

        return compact('columns', 'rows');
    }

    private function exportUsers(Request $request): array
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $users = $query->latest()->get();

        $columns = ['ID', 'Nom', 'Email', 'Téléphone', 'Rôle', 'Statut', 'Ville', 'Inscrit le'];

        $rows = $users->map(fn ($u) => [
            $u->id,
            $u->name,
            $u->email,
            $u->telephone ?? '',
            $u->role,
            $u->statut ?? 'actif',
            $u->ville ?? '',
            $u->created_at?->format('d/m/Y') ?? '',
        ])->toArray();

        return compact('columns', 'rows');
    }

    private function exportRepayments(Request $request): array
    {
        $query = Repayment::with('project.owner');

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $repayments = $query->latest()->get();

        $columns = ['ID', 'Projet', 'Porteur', 'Montant Total', 'Montant Payé', 'Statut', 'Date Échéance', 'Date Paiement'];

        $rows = $repayments->map(fn ($r) => [
            $r->id,
            $r->project?->titre ?? '',
            $r->project?->owner?->name ?? '',
            number_format((float) $r->montant_total, 0, ',', ' ').' FCFA',
            number_format((float) ($r->montant_rembourse ?? 0), 0, ',', ' ').' FCFA',
            $r->statut ?? '',
            $r->date_echeance?->format('d/m/Y') ?? '',
            $r->date_paiement?->format('d/m/Y') ?? '',
        ])->toArray();

        return compact('columns', 'rows');
    }

    private function exportFundings(Request $request): array
    {
        $query = Funding::with('project', 'institution');

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $fundings = $query->latest()->get();

        $columns = ['ID', 'Projet', 'Institution', 'Montant', 'Type', 'Statut', 'Date'];

        $rows = $fundings->map(fn ($f) => [
            $f->id,
            $f->project?->titre ?? '',
            $f->institution?->nom ?? '',
            number_format((float) ($f->montant_valide ?? $f->montant ?? 0), 0, ',', ' ').' FCFA',
            $f->type ?? 'loan',
            $f->statut ?? '',
            $f->created_at?->format('d/m/Y') ?? '',
        ])->toArray();

        return compact('columns', 'rows');
    }

    private function exportEcheances(Request $request): array
    {
        $query = Echeance::with('project.owner');

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $echeances = $query->latest()->get();

        $columns = ['ID', 'Projet', 'Porteur', 'Montant', 'Statut', 'Date Échéance', 'Payé le'];

        $rows = $echeances->map(fn ($e) => [
            $e->id,
            $e->project?->titre ?? '',
            $e->project?->owner?->name ?? '',
            number_format((float) $e->montant_total, 0, ',', ' ').' FCFA',
            $e->statut ?? '',
            $e->date_echeance?->format('d/m/Y') ?? '',
            $e->date_paiement?->format('d/m/Y') ?? '',
        ])->toArray();

        return compact('columns', 'rows');
    }
}
