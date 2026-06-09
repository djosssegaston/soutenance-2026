<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\InterviewHistory;
use App\Models\Project;
use App\Models\RendezVous;
use App\Models\UserNotification;
use App\Services\InterviewService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    protected InterviewService $interviewService;

    public function __construct(InterviewService $interviewService)
    {
        $this->interviewService = $interviewService;
    }

    /**
     * Liste des entretiens pour l'institution
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'institution') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $institution = $user->institution;
        if (! $institution) {
            return response()->json(['data' => [], 'links' => [], 'meta' => ['current_page' => 1, 'last_page' => 1, 'total' => 0]]);
        }

        $query = Interview::where('institution_id', $institution->id)
            ->with(['project', 'porteur', 'analyste']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date')) {
            $query->where('date_entretien', $request->date);
        }

        $interviews = $query->latest()->paginate(10);

        return response()->json($interviews);
    }

    /**
     * Statistiques des entretiens
     */
    public function stats(Request $request)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json([
                'aujourdhui' => 0,
                'semaine' => 0,
                'confirmes' => 0,
                'en_attente' => 0,
                'annules' => 0,
            ]);
        }

        $institutionId = $institution->id;
        $today = now()->toDateString();

        $stats = [
            'aujourdhui' => Interview::where('institution_id', $institutionId)->where('date_entretien', $today)->count(),
            'semaine' => Interview::where('institution_id', $institutionId)
                ->whereBetween('date_entretien', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'confirmes' => Interview::where('institution_id', $institutionId)->where('statut', 'confirme')->count(),
            'en_attente' => Interview::where('institution_id', $institutionId)->where('statut', 'programme')->count(),
            'annules' => Interview::where('institution_id', $institutionId)->where('statut', 'annule')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Création d'un entretien
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date_entretien' => 'required|date|after_or_equal:today',
            'heure_entretien' => 'required',
            'type_entretien' => 'required',
            'titre' => 'required|string|max:255',
            'lieu' => 'nullable|string',
        ]);

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $project = Project::findOrFail($request->project_id);

        if (in_array($project->statut, [\App\Enums\ProjectStatus::FUNDED->value, \App\Enums\ProjectStatus::ACTIVE->value, \App\Enums\ProjectStatus::COMPLETED->value])) {
            return response()->json(['message' => 'Ce projet n\'est plus disponible pour les entretiens.'], 403);
        }

        $data = $request->all();
        $data['institution_id'] = $institution->id;
        $data['porteur_id'] = $project->user_id;
        $data['analyste_id'] = $request->user()->id;
        $data['statut'] = 'programme';

        $interview = $this->interviewService->createInterview($data, $request->user()->name);

        // Créer une entrée dans rendez_vous pour que le porteur voie le RDV
        RendezVous::create([
            'user_id' => $project->user_id,
            'institution_id' => $institution->id,
            'project_id' => $project->id,
            'interview_id' => $interview->id,
            'from_institution' => true,
            'date_heure' => $request->date_entretien.' '.$request->heure_entretien,
            'objet' => $request->titre,
            'description' => $request->description ?? '',
            'lieu' => $request->lieu ?? '',
            'statut' => 'planifie',
        ]);

        UserNotification::create([
            'user_id' => $project->user_id,
            'type' => 'entretien_planifie',
            'title' => 'Entretien planifié',
            'content' => 'Un entretien a été planifié pour votre projet "'.$project->titre.'" le '.$request->date_entretien.' à '.$request->heure_entretien.'.',
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'entretien_planifie',
            'title' => 'Entretien créé',
            'content' => 'Vous avez planifié un entretien pour le projet "'.$project->titre.'" le '.$request->date_entretien.' à '.$request->heure_entretien.'.',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Entretien créé avec succès', 'interview' => $interview], 201);
    }

    /**
     * Détails d'un entretien
     */
    public function show(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $interview = Interview::with(['project', 'porteur', 'analyste', 'histories'])
            ->where('institution_id', $institution->id)
            ->findOrFail($id);

        return response()->json($interview);
    }

    /**
     * Confirmation de présence
     */
    public function confirm(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $interview = Interview::where('institution_id', $institution->id)->findOrFail($id);
        $this->interviewService->updateStatus($interview, 'confirme', $request->user()->name);

        // Sync rendez_vous
        RendezVous::where('interview_id', $interview->id)
            ->where('from_institution', true)
            ->update(['statut' => 'accepte']);

        UserNotification::create([
            'user_id' => $interview->porteur_id,
            'type' => 'entretien_confirme',
            'title' => 'Entretien confirmé',
            'content' => 'L\'entretien pour le projet "'.$interview->project->titre.'" du '.$interview->date_entretien.' a été confirmé.',
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'entretien_confirme',
            'title' => 'Entretien confirmé',
            'content' => 'Vous avez confirmé l\'entretien pour le projet "'.$interview->project->titre.'".',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Entretien confirmé', 'interview' => $interview->fresh()]);
    }

    /**
     * Annulation d'un entretien
     */
    public function cancel(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $interview = Interview::where('institution_id', $institution->id)->findOrFail($id);
        $this->interviewService->updateStatus($interview, 'annule', $request->user()->name, $request->commentaire ?? '');

        // Sync rendez_vous
        RendezVous::where('interview_id', $interview->id)
            ->where('from_institution', true)
            ->update(['statut' => 'annule']);

        $motif = $request->commentaire ?? 'Motif non spécifié';

        UserNotification::create([
            'user_id' => $interview->porteur_id,
            'type' => 'entretien_annule',
            'title' => 'Entretien annulé',
            'content' => 'L\'entretien pour le projet "'.$interview->project->titre.'" a été annulé. Motif: '.$motif,
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'entretien_annule',
            'title' => 'Entretien annulé',
            'content' => 'Vous avez annulé l\'entretien pour le projet "'.$interview->project->titre.'".',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Entretien annulé', 'interview' => $interview]);
    }

    /**
     * Ajout d'un compte rendu
     */
    public function report(Request $request, $id)
    {
        $request->validate([
            'compte_rendu' => 'required|string',
            'decision_preliminaire' => 'required|string',
        ]);

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $interview = Interview::where('institution_id', $institution->id)->findOrFail($id);

        $interview->update([
            'compte_rendu' => $request->compte_rendu,
            'decision_preliminaire' => $request->decision_preliminaire,
            'statut' => 'termine',
        ]);

        // Sync rendez_vous
        RendezVous::where('interview_id', $interview->id)
            ->where('from_institution', true)
            ->update(['statut' => 'termine']);

        InterviewHistory::create([
            'interview_id' => $interview->id,
            'action' => 'compte_rendu_ajoute',
            'auteur' => $request->user()->name,
            'details' => "Compte rendu rédigé. Décision: {$request->decision_preliminaire}",
        ]);

        UserNotification::create([
            'user_id' => $interview->porteur_id,
            'type' => 'entretien_termine',
            'title' => 'Compte rendu disponible',
            'content' => 'Le compte rendu de l\'entretien pour le projet "'.$interview->project->titre.'" est disponible. Décision préliminaire: '.$request->decision_preliminaire,
            'is_read' => false,
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'type' => 'entretien_termine',
            'title' => 'Compte rendu enregistré',
            'content' => 'Vous avez enregistré le compte rendu pour l\'entretien du projet "'.$interview->project->titre.'".',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Compte rendu enregistré', 'interview' => $interview->fresh()]);
    }

    /**
     * Generate convocation PDF for an interview
     */
    public function convocation(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $interview = Interview::with(['project', 'porteur', 'institution'])
            ->where('institution_id', $institution->id)
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.convocation', [
            'interview' => $interview,
            'institution' => $interview->institution,
            'porteur' => $interview->porteur,
            'project' => $interview->project,
            'date' => now()->format('d/m/Y'),
        ]);

        return $pdf->download('convocation_entretien_'.$interview->id.'.pdf');
    }
}
