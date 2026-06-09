<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\ProjectWorkflowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ProjectWorkflowService $workflow
    ) {}

    public function porteurProjectsList(Request $request)
    {
        $user = $request->user();
        $projects = Project::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'titre', 'statut']);

        return response()->json(['data' => $projects]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Project::query()->latest();

        if ($user->role === 'porteur') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'institution') {
            $query->whereIn('statut', ProjectStatus::institutionVisibleValues());
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'porteur') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'secteur' => 'nullable|string|max:255',
            'montant_demande' => 'required|numeric|min:0',
            'duree' => 'nullable|string|max:255',
            'localisation' => 'nullable|string|max:255',
        ]);

        $project = Project::create([
            ...$data,
            'user_id' => $request->user()->id,
            'montant_finance' => 0,
            'statut' => ProjectStatus::DRAFT->value,
        ]);

        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::DRAFT->value,
            'new_status' => ProjectStatus::DRAFT->value,
            'reason' => 'Projet créé',
            'actor_id' => $request->user()->id,
            'metadata' => [
                'source' => 'api.projects.store',
            ],
        ]);

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json(
            $project->load(['documents', 'validations', 'analyses', 'financements', 'remboursements', 'comments'])
        );
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $status = $project->statusEnum();
        if ($request->user()->role !== 'admin' && ! $status->canOwnerModify()) {
            return response()->json([
                'success' => false,
                'message' => 'Projet déjà engagé dans le workflow: modification interdite.',
            ], 409);
        }

        $data = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'secteur' => 'nullable|string|max:255',
            'montant_demande' => 'nullable|numeric|min:0',
            'duree' => 'nullable|string|max:255',
            'localisation' => 'nullable|string|max:255',
        ]);

        $project->update($data);

        return response()->json(['success' => true, 'project' => $project->fresh()]);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        if (auth()->user()?->role !== 'admin' && ! $project->statusEnum()->canOwnerModify()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un projet déjà engagé.',
            ], 409);
        }

        $project->delete();

        return response()->json(['success' => true, 'message' => 'Projet supprimé']);
    }

    public function submit(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        return $this->submitProject($request, $project);
    }

    public function submitDraft(Request $request, int $id)
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['success' => false, 'message' => 'Projet non trouvé'], 404);
        }

        $this->authorize('update', $project);

        return $this->submitProject($request, $project);
    }

    private function submitProject(Request $request, Project $project)
    {
        if ($request->user()->role !== 'porteur' || $project->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $status = $project->statusEnum();
        if (! $status->canOwnerSubmit()) {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les brouillons ou projets rejetés peuvent être soumis.',
            ], 409);
        }

        $this->workflow->submit($project, $request->user()->id);

        UserNotification::create([
            'user_id' => User::where('role', 'admin')->first()?->id,
            'type' => 'nouveau_projet',
            'title' => 'Nouveau projet soumis',
            'content' => "Le projet \"{$project->titre}\" a été soumis pour validation.",
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $project->user_id,
            'type' => 'projet_soumis',
            'title' => 'Projet soumis',
            'content' => "Votre projet \"{$project->titre}\" a été soumis pour validation auprès de l'administration.",
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Projet soumis avec succès.',
            'project' => $project->fresh(),
        ]);
    }
}
