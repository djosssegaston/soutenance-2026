<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentRule;
use App\Models\ProjectDocument;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class AdminDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectDocument::with(['project.owner'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('statut_validation', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $documents = $query->paginate(20);

        $stats = [
            'total' => ProjectDocument::count(),
            'pending' => ProjectDocument::where('statut_validation', 'en_attente')->count(),
            'validated' => ProjectDocument::where('statut_validation', 'valide')->count(),
            'rejected' => ProjectDocument::where('statut_validation', 'rejete')->count(),
        ];

        $data = $documents->map(fn ($d) => [
            'id' => $d->id,
            'project_id' => $d->project_id,
            'project_title' => $d->project?->titre ?? '',
            'porteur_name' => $d->project?->owner?->name ?? '',
            'type' => $d->type,
            'fichier' => $d->fichier,
            'statut_validation' => $d->statut_validation ?? 'en_attente',
            'uploaded_at' => $d->uploaded_at?->format('d/m/Y H:i') ?? $d->created_at?->format('d/m/Y H:i'),
        ]);

        return response()->json([
            'success' => true,
            'total' => $documents->total(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function validateDocument(Request $request, $id)
    {
        $document = ProjectDocument::with('project.owner')->findOrFail($id);

        $rules = $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $document->update([
            'statut_validation' => 'valide',
        ]);

        UserNotification::create([
            'user_id' => $document->project->user_id,
            'type' => 'document_valide',
            'title' => 'Document validé',
            'content' => 'Votre document "'.$document->type.'" pour le projet "'.$document->project->titre.'" a été validé.',
            'is_read' => false,
        ]);

        AuditLog::log(auth()->id(), 'Document validé: '.$document->type.' (projet #'.$document->project_id.')', 'bi-file-check', 'success', 'info');

        return response()->json([
            'success' => true,
            'message' => 'Document validé avec succès.',
        ]);
    }

    public function rejectDocument(Request $request, $id)
    {
        $document = ProjectDocument::with('project.owner')->findOrFail($id);

        $rules = $request->validate([
            'motif' => 'required|string|max:1000',
        ]);

        $document->update([
            'statut_validation' => 'rejete',
            'raison_rejet' => $request->motif,
        ]);

        UserNotification::create([
            'user_id' => $document->project->user_id,
            'type' => 'document_refuse',
            'title' => 'Document refusé',
            'content' => 'Votre document "'.$document->type.'" pour le projet "'.$document->project->titre.'" a été refusé. Motif: '.$request->motif,
            'is_read' => false,
        ]);

        AuditLog::log(auth()->id(), 'Document refusé: '.$document->type.' (projet #'.$document->project_id.') motif: '.$request->motif, 'bi-file-x', 'danger', 'warning');

        return response()->json([
            'success' => true,
            'message' => 'Document refusé.',
        ]);
    }

    public function checklists()
    {
        $rules = DocumentRule::with('secteur')
            ->active()
            ->ordered()
            ->get()
            ->groupBy(fn ($r) => $r->secteur?->nom ?? 'Général');

        return response()->json([
            'success' => true,
            'data' => $rules->map(fn ($items, $secteur) => [
                'secteur' => $secteur,
                'documents' => $items->map(fn ($r) => [
                    'id' => $r->id,
                    'label' => $r->label,
                    'slug' => $r->slug,
                    'obligatoire' => $r->obligatoire,
                    'acteur' => $r->acteur,
                    'types_mime' => $r->types_mime,
                    'max_size' => $r->max_size,
                    'description' => $r->description,
                ]),
            ])->values(),
        ]);
    }

    public function projectChecklist($projectId)
    {
        $project = \App\Models\Project::with('documents')->findOrFail($projectId);
        $rules = DocumentRule::active()->forRole('porteur')->ordered()->get();

        $uploadedTypes = $project->documents->pluck('type')->toArray();

        $checklist = $rules->map(fn ($rule) => [
            'rule_id' => $rule->id,
            'label' => $rule->label,
            'slug' => $rule->slug,
            'obligatoire' => $rule->obligatoire,
            'uploaded' => in_array($rule->slug, $uploadedTypes),
            'document' => $project->documents->firstWhere('type', $rule->slug),
            'status' => $this->getDocumentStatus($rule, $project->documents->firstWhere('type', $rule->slug)),
        ]);

        $stats = [
            'total' => $rules->count(),
            'obligatoire' => $rules->where('obligatoire', true)->count(),
            'uploaded' => count($uploadedTypes),
            'completion' => $rules->count() > 0 ? round((count($uploadedTypes) / $rules->count()) * 100) : 0,
        ];

        return response()->json([
            'success' => true,
            'project' => ['id' => $project->id, 'titre' => $project->titre],
            'stats' => $stats,
            'checklist' => $checklist,
        ]);
    }

    private function getDocumentStatus($rule, $document): string
    {
        if (! $document) {
            return 'manquant';
        }

        return match ($document->statut_validation) {
            'valide' => 'valide',
            'rejete' => 'rejete',
            default => 'en_attente',
        };
    }
}
