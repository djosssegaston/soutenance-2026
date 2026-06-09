<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Models\UserNotification;
use App\Services\ProjectWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstitutionAnalysisController extends Controller
{
    public function __construct(
        protected ProjectWorkflowService $workflow
    ) {}

    public function store(Request $request, Project $project)
    {
        if ($request->user()->role !== 'institution') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution introuvable pour cet utilisateur.'], 422);
        }

        $data = $request->validate([
            'risk_score' => 'nullable|integer|min:0|max:100',
            'commentaire' => 'nullable|string',
            'entretien_date' => 'nullable|date',
        ]);

        try {
            $analysis = DB::transaction(function () use ($request, $project, $institution, $data) {
                $status = $project->statusEnum();
                if ($status === ProjectStatus::ADMIN_VALIDATED) {
                    $this->workflow->startInstitutionReview($project, $request->user()->id);
                } elseif (! in_array($status, [
                    ProjectStatus::UNDER_INSTITUTION_REVIEW,
                    ProjectStatus::INTERVIEW_SCHEDULED,
                    ProjectStatus::INTERVIEW_CONFIRMED,
                    ProjectStatus::DOCUMENTS_REQUESTED,
                    ProjectStatus::INSTITUTION_ACCEPTED,
                ], true)) {
                    throw new RuntimeException('Ce projet ne peut pas être analysé dans son état actuel.');
                }

                return InstitutionAnalysis::updateOrCreate(
                    ['project_id' => $project->id, 'institution_id' => $institution->id],
                    [
                        'statut' => 'en_analyse',
                        'risk_score' => $data['risk_score'] ?? 0,
                        'commentaire' => $data['commentaire'] ?? null,
                        'entretien_date' => $data['entretien_date'] ?? null,
                    ]
                );
            });
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        UserNotification::create([
            'user_id' => $project->user_id,
            'type' => 'analyse_institution',
            'title' => 'Analyse en cours',
            'content' => 'Une institution a démarré l\'analyse de votre projet "'.$project->titre.'".',
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'analyse_institution',
            'title' => 'Analyse démarrée',
            'content' => 'Vous avez démarré l\'analyse du projet "'.$project->titre.'".',
            'is_read' => false,
        ]);

        AuditLog::log($request->user()->id, 'Analyse démarrée: '.$project->titre, 'bi-search', 'info', 'primary');

        return response()->json($analysis->fresh(), 201);
    }

    public function accept(Request $request, Project $project)
    {
        if ($request->user()->role !== 'institution') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution introuvable pour cet utilisateur.'], 422);
        }

        $user = $request->user();
        $userId = $user->id;

        try {
            DB::transaction(function () use ($project, $institution, $userId) {
                $analysis = InstitutionAnalysis::firstOrCreate(
                    ['project_id' => $project->id, 'institution_id' => $institution->id],
                    ['statut' => 'en_analyse', 'risk_score' => 0]
                );

                if ($project->statusEnum() === ProjectStatus::ADMIN_VALIDATED) {
                    $this->workflow->startInstitutionReview($project, $userId);
                }

                $status = $project->statusEnum();
                if (! in_array($status, [
                    ProjectStatus::UNDER_INSTITUTION_REVIEW,
                    ProjectStatus::INTERVIEW_CONFIRMED,
                ], true)) {
                    throw new RuntimeException('Le projet doit être en analyse institutionnelle avant acceptation.');
                }

                $this->workflow->institutionAccept($project, $userId);
                $analysis->update(['statut' => 'accepte']);

                AuditLog::log($userId, 'Projet accepté: '.$project->titre, 'bi-check-circle', 'success', 'success');
            });
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        UserNotification::create([
            'user_id' => $project->user_id,
            'type' => 'analyse_institution',
            'title' => 'Projet accepté',
            'content' => 'Votre projet "'.$project->titre.'" a été accepté par une institution. Une conversation a été ouverte.',
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'analyse_institution',
            'title' => 'Projet accepté',
            'content' => 'Vous avez accepté le projet "'.$project->titre.'".',
            'is_read' => false,
        ]);

        // Automatiquement créer la discussion
        try {
            $conversationService = app(\App\Services\ConversationService::class);
            $conversationService->createForProject($project, $institution, $request->user());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur création conversation auto: '.$e->getMessage());
        }

        return response()->json($project->fresh());
    }
}
