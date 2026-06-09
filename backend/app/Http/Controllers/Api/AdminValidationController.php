<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectValidation;
use App\Models\UserNotification;
use App\Services\ProjectWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdminValidationController extends Controller
{
    public function __construct(
        protected ProjectWorkflowService $workflow
    ) {}

    public function store(Request $request, Project $project)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'decision' => 'required|string|in:valide,rejete',
            'commentaire' => 'nullable|string',
        ]);

        return $data['decision'] === 'valide'
            ? $this->approve($request, $project, $data['commentaire'] ?? null)
            : $this->rejectProject($request, $project, $data['commentaire'] ?? null);
    }

    public function validate(Request $request, int $id)
    {
        $project = Project::find($id);
        if (! $project) {
            return response()->json(['success' => false, 'message' => 'Projet non trouvé'], 404);
        }

        return $this->approve($request, $project, $request->input('commentaire'));
    }

    public function reject(Request $request, int $id)
    {
        $project = Project::find($id);
        if (! $project) {
            return response()->json(['success' => false, 'message' => 'Projet non trouvé'], 404);
        }

        return $this->rejectProject($request, $project, $request->input('commentaire'));
    }

    private function approve(Request $request, Project $project, ?string $commentaire)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        try {
            DB::transaction(function () use ($request, $project, $commentaire) {
                $status = $project->statusEnum();
                if ($status === ProjectStatus::UNDER_ADMIN_REVIEW) {
                    $this->workflow->adminValidate($project, $request->user()->id);
                } elseif ($status === ProjectStatus::SUBMITTED) {
                    $this->workflow->adminValidate($project, $request->user()->id);
                } elseif ($status !== ProjectStatus::ADMIN_VALIDATED) {
                    throw new RuntimeException('Ce projet ne peut pas être validé dans son état actuel.');
                }

                ProjectValidation::create([
                    'project_id' => $project->id,
                    'admin_id' => $request->user()->id,
                    'decision' => 'valide',
                    'commentaire' => $commentaire,
                    'date_decision' => now(),
                ]);

                UserNotification::create([
                    'user_id' => $project->user_id,
                    'type' => 'validation_admin',
                    'title' => 'Projet validé',
                    'content' => 'Votre projet a été validé par l\'administration.',
                    'is_read' => false,
                ]);
            });
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
        }

        return response()->json([
            'success' => true,
            'project' => $project->fresh(),
            'status_label' => ProjectStatus::ADMIN_VALIDATED->label(),
            'status_class' => ProjectStatus::ADMIN_VALIDATED->badgeClass(),
        ]);
    }

    private function rejectProject(Request $request, Project $project, ?string $commentaire)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        try {
            DB::transaction(function () use ($request, $project, $commentaire) {
                $status = $project->statusEnum();
                if (! in_array($status, [ProjectStatus::SUBMITTED, ProjectStatus::UNDER_ADMIN_REVIEW], true)) {
                    throw new RuntimeException('Ce projet ne peut pas être rejeté dans son état actuel.');
                }

                $this->workflow->adminReject(
                    $project,
                    $request->user()->id,
                    $commentaire ?: 'Projet rejeté par l’administration.'
                );

                ProjectValidation::create([
                    'project_id' => $project->id,
                    'admin_id' => $request->user()->id,
                    'decision' => 'rejete',
                    'commentaire' => $commentaire,
                    'date_decision' => now(),
                ]);

                UserNotification::create([
                    'user_id' => $project->user_id,
                    'type' => 'validation_admin',
                    'title' => 'Projet rejeté',
                    'content' => 'Votre projet a été rejeté par l\'administration. Motif: '.($commentaire ?? 'Non spécifié'),
                    'is_read' => false,
                ]);
            });
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
        }

        return response()->json([
            'success' => true,
            'project' => $project->fresh(),
            'status_label' => ProjectStatus::ADMIN_REJECTED->label(),
            'status_class' => ProjectStatus::ADMIN_REJECTED->badgeClass(),
        ]);
    }
}
