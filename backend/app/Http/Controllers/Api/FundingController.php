<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Funding;
use App\Models\Project;
use App\Models\UserNotification;
use App\Services\ProjectWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FundingController extends Controller
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
            'montant' => 'required|numeric|min:0',
            'date_financement' => 'nullable|date',
        ]);

        try {
            $funding = DB::transaction(function () use ($request, $project, $institution, $data) {
                $status = $project->statusEnum();

                if ($status === ProjectStatus::ADMIN_VALIDATED) {
                    $this->workflow->startInstitutionReview($project, $request->user()->id);
                    $status = $project->fresh()->statusEnum();
                }

                if ($status === ProjectStatus::UNDER_INSTITUTION_REVIEW) {
                    $this->workflow->institutionAccept($project, $request->user()->id);
                    $status = $project->fresh()->statusEnum();
                }

                if ($status !== ProjectStatus::INSTITUTION_ACCEPTED && ! $status->isFinanced()) {
                    throw new RuntimeException('Le projet doit être accepté par une institution avant financement.');
                }

                $existing = Funding::where('project_id', $project->id)
                    ->where('institution_id', $institution->id)
                    ->first();

                if ($existing) {
                    throw new RuntimeException('Un financement existe déjà pour cette institution et ce projet.');
                }

                $funding = Funding::create([
                    'project_id' => $project->id,
                    'institution_id' => $institution->id,
                    'montant' => $data['montant'],
                    'date_financement' => $data['date_financement'] ?? now(),
                    'statut' => 'finance',
                ]);

                $project->update([
                    'montant_finance' => (float) $project->montant_finance + (float) $data['montant'],
                ]);

                if (! $project->fresh()->statusEnum()->isFinanced()) {
                    $this->workflow->markAsFunded($project->fresh(), $request->user()->id);
                }

                return $funding;
            });
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        UserNotification::create([
            'user_id' => $project->user_id,
            'type' => 'financement',
            'title' => 'Financement reçu',
            'content' => 'Un financement de '.number_format((float) $data['montant'], 0, ',', ' ').' FCFA a été enregistré pour votre projet "'.$project->titre.'".',
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'financement',
            'title' => 'Financement enregistré',
            'content' => 'Vous avez enregistré un financement de '.number_format((float) $data['montant'], 0, ',', ' ').' FCFA pour le projet "'.$project->titre.'".',
            'is_read' => false,
        ]);

        return response()->json($funding->fresh(), 201);
    }
}
