<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\RendezVous;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RendezVous::with(['institution', 'project'])
            ->where('user_id', $request->user()->id)
            ->orderBy('date_heure', 'desc');

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'institution_id' => ['required', 'exists:institutions,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'date_heure' => ['required', 'date'],
            'objet' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lieu' => ['nullable', 'string', 'max:255'],
        ]);

        // Règle métier : le projet ne doit pas être déjà financé
        if (! empty($data['project_id'])) {
            $project = \App\Models\Project::find($data['project_id']);
            if ($project && in_array($project->statut, [\App\Enums\ProjectStatus::FUNDED->value, \App\Enums\ProjectStatus::ACTIVE->value, \App\Enums\ProjectStatus::COMPLETED->value])) {
                return response()->json(['message' => 'Ce projet n\'est plus disponible pour les rendez-vous.'], 403);
            }
        }

        // Règle métier : l'institution doit avoir déjà envoyé au moins un rendez-vous au porteur
        $existingRdv = RendezVous::where('user_id', $userId)
            ->where('institution_id', $data['institution_id'])
            ->exists();

        if (! $existingRdv) {
            return response()->json([
                'message' => 'Vous ne pouvez demander un rendez-vous qu\'avec une institution qui vous en a déjà envoyé au moins un.',
            ], 422);
        }

        // Règle métier : pas deux rendez-vous à la même date/heure
        $conflict = RendezVous::where('user_id', $userId)
            ->where('date_heure', $data['date_heure'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Vous avez déjà un rendez-vous programmé à cette date et heure.',
            ], 422);
        }

        $rdv = RendezVous::create(array_merge($data, ['user_id' => $userId]));

        return response()->json($rdv->load(['institution', 'project']), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $rdv = RendezVous::with(['institution', 'project'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($rdv);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $rdv = RendezVous::where('user_id', $request->user()->id)->findOrFail($id);

        if ($rdv->from_institution) {
            return response()->json(['message' => 'Ce rendez-vous a été créé par l\'institution et ne peut pas être modifié.'], 403);
        }

        $data = $request->validate([
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'date_heure' => ['sometimes', 'date'],
            'objet' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'statut' => ['sometimes', 'in:planifie,accepte,rejete,termine,annule'],
            'notes_porteur' => ['nullable', 'string'],
        ]);

        // Règle métier : pas deux rendez-vous à la même date/heure (sauf celui-ci)
        if (isset($data['date_heure'])) {
            $conflict = RendezVous::where('user_id', $request->user()->id)
                ->where('date_heure', $data['date_heure'])
                ->where('id', '!=', $rdv->id)
                ->exists();

            if ($conflict) {
                return response()->json([
                    'message' => 'Vous avez déjà un rendez-vous programmé à cette date et heure.',
                ], 422);
            }
        }

        $rdv->update($data);

        return response()->json($rdv->fresh(['institution', 'project']));
    }

    public function accept(Request $request, string $id): JsonResponse
    {
        $rdv = RendezVous::where('user_id', $request->user()->id)->findOrFail($id);
        $rdv->update(['statut' => 'accepte']);

        // Sync interview if linked
        if ($rdv->interview_id && $rdv->from_institution) {
            $interview = Interview::find($rdv->interview_id);
            if ($interview && $interview->statut === 'programme') {
                $interview->update(['statut' => 'confirme']);
            }
        }

        // Notify institution
        if ($rdv->institution_id) {
            $institutionUser = $rdv->institution->user;
            if ($institutionUser) {
                UserNotification::create([
                    'user_id' => $institutionUser->id,
                    'type' => 'rdv_accepte',
                    'title' => 'Rendez-vous accepté',
                    'content' => 'Le porteur '.$request->user()->name.' a accepté le rendez-vous "'.$rdv->objet.'".',
                    'is_read' => false,
                ]);
            }
        }

        return response()->json(['message' => 'Rendez-vous accepté.', 'rdv' => $rdv->fresh()]);
    }

    public function reject(Request $request, string $id): JsonResponse
    {
        $rdv = RendezVous::where('user_id', $request->user()->id)->findOrFail($id);
        $rdv->update(['statut' => 'rejete']);

        // Sync interview if linked
        if ($rdv->interview_id && $rdv->from_institution) {
            $interview = Interview::find($rdv->interview_id);
            if ($interview && $interview->statut === 'programme') {
                $interview->update(['statut' => 'annule']);
            }
        }

        // Notify institution
        if ($rdv->institution_id) {
            $institutionUser = $rdv->institution->user;
            if ($institutionUser) {
                UserNotification::create([
                    'user_id' => $institutionUser->id,
                    'type' => 'rdv_refuse',
                    'title' => 'Rendez-vous refusé',
                    'content' => 'Le porteur '.$request->user()->name.' a refusé le rendez-vous "'.$rdv->objet.'".',
                    'is_read' => false,
                ]);
            }
        }

        return response()->json(['message' => 'Rendez-vous rejeté.', 'rdv' => $rdv->fresh()]);
    }

    public function destroy(string $id): JsonResponse
    {
        $rdv = RendezVous::where('user_id', auth()->id())->findOrFail($id);

        if ($rdv->from_institution) {
            return response()->json(['message' => 'Ce rendez-vous a été créé par l\'institution et ne peut pas être supprimé.'], 403);
        }

        $rdv->delete();

        return response()->json(['message' => 'Rendez-vous supprimé.']);
    }
}
