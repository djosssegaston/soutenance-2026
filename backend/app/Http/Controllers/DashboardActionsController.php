<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectStatusHistory;
use App\Models\ProjectValidation;
use App\Models\Repayment;
use App\Models\UserNotification;
use App\Services\ProjectWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DashboardActionsController extends Controller
{
    public function __construct(
        protected ProjectWorkflowService $workflow
    ) {}

    public function adminValidate(Request $request, Project $project)
    {
        $admin = $request->user();
        $commentaire = $request->input('commentaire', 'Validation admin du projet.');

        try {
            DB::transaction(function () use ($admin, $project, $commentaire) {
                // Utiliser la state machine
                $this->workflow->adminValidate($project, $admin->id);

                // Créer la validation
                ProjectValidation::create([
                    'project_id' => $project->id,
                    'admin_id' => $admin->id,
                    'decision' => 'valide',
                    'commentaire' => $commentaire,
                ]);

                // Notification
                UserNotification::create([
                    'user_id' => $project->user_id,
                    'type' => 'validation_projet',
                    'content' => 'Votre projet '.$project->titre.' a ete valide par l\'administration.',
                    'is_read' => false,
                ]);
            });

            return back()->with('success', 'Projet validé avec succès.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function adminReject(Request $request, Project $project)
    {
        $admin = $request->user();
        $commentaire = $request->input('commentaire', 'Rejet du projet.');

        try {
            DB::transaction(function () use ($admin, $project, $commentaire) {
                // Utiliser la state machine
                $this->workflow->adminReject($project, $admin->id, $commentaire);

                // Créer la validation
                ProjectValidation::create([
                    'project_id' => $project->id,
                    'admin_id' => $admin->id,
                    'decision' => 'rejete',
                    'commentaire' => $commentaire,
                ]);

                // Notification
                UserNotification::create([
                    'user_id' => $project->user_id,
                    'type' => 'refus_projet',
                    'content' => 'Votre projet '.$project->titre.' a ete refuse apres analyse. Raison: '.$commentaire,
                    'is_read' => false,
                ]);
            });

            return back()->with('success', 'Projet rejeté.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function institutionAnalyze(Request $request, Project $project)
    {
        $institution = $request->user()->institution;

        try {
            DB::transaction(function () use ($institution, $project, $request) {
                // Créer ou mettre à jour l'analyse
                $riskScore = min(95, max(30, (int) round(
                    50
                    + ($project->montant_demande > 10000000 ? 20 : ($project->montant_demande > 5000000 ? 10 : 0))
                    - ($project->documents()->count() * 5)
                )));

                $analysis = InstitutionAnalysis::firstOrCreate(
                    ['project_id' => $project->id, 'institution_id' => $institution->id],
                    [
                        'statut' => 'en_analyse',
                        'risk_score' => $riskScore,
                    ]
                );

                if ($project->statusEnum() === ProjectStatus::ADMIN_VALIDATED) {
                    $this->workflow->startInstitutionReview($project, $request->user()->id);
                }
            });

            return back()->with('success', 'Analyse démarrée.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function institutionInterview(Request $request, Project $project)
    {
        $institution = $request->user()->institution;

        try {
            DB::transaction(function () use ($institution, $project, $request) {
                $riskScore = min(95, max(30, (int) round(
                    50
                    + ($project->montant_demande > 10000000 ? 20 : ($project->montant_demande > 5000000 ? 10 : 0))
                    - ($project->documents()->count() * 5)
                )));

                InstitutionAnalysis::firstOrCreate(
                    ['project_id' => $project->id, 'institution_id' => $institution->id],
                    [
                        'statut' => 'en_analyse',
                        'risk_score' => $riskScore,
                    ]
                );

                // Utiliser la state machine
                $this->workflow->scheduleInterview($project, $request->user()->id);

                // Notification
                UserNotification::create([
                    'user_id' => $project->user_id,
                    'type' => 'entretien_planifie',
                    'content' => 'Un entretien a ete planifie pour votre projet '.$project->titre.'.',
                    'is_read' => false,
                ]);
            });

            return back()->with('success', 'Entretien planifié.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function institutionAccept(Request $request, Project $project)
    {
        try {
            DB::transaction(function () use ($project, $request) {
                $this->workflow->institutionAccept($project, $request->user()->id);

                // Auto-create conversation (same as API version)
                try {
                    $institution = $request->user()->institution;
                    if ($institution) {
                        $conversationService = app(\App\Services\ConversationService::class);
                        $conversationService->createForProject($project, $institution, $request->user());
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur creation conversation auto: '.$e->getMessage());
                }
            });

            return back()->with('success', 'Projet accepté par l\'institution.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function institutionFinance(Request $request, Project $project)
    {
        $institution = $request->user()->institution;

        try {
            DB::transaction(function () use ($project, $institution, $request) {
                // Vérifier si le financement existe déjà
                $existing = Funding::where('project_id', $project->id)
                    ->where('institution_id', $institution->id)
                    ->first();

                if ($existing) {
                    throw new RuntimeException('Financement déjà enregistré.');
                }

                // Créer le financement
                $funding = Funding::create([
                    'project_id' => $project->id,
                    'institution_id' => $institution->id,
                    'montant' => $project->montant_demande,
                    'date_financement' => now(),
                    'statut' => 'finance',
                ]);

                // Utiliser la state machine
                $this->workflow->markAsFunded($project, $request->user()->id);

                // Créer l'échéancier de remboursement
                $this->createRepaymentSchedule($project, $funding->montant);

                // Notification
                UserNotification::create([
                    'user_id' => $project->user_id,
                    'type' => 'financement_recu',
                    'content' => 'Le financement pour votre projet '.$project->titre.' est disponible. Merci de confirmer.',
                    'is_read' => false,
                ]);
            });

            return back()->with('success', 'Financement enregistré avec succès.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function createRepaymentSchedule(Project $project, int|float|string $amount): void
    {
        $amount = (float) $amount;
        $months = 6;
        if (! empty($project->duree) && preg_match('/(\d+)/', $project->duree, $matches)) {
            $months = max(3, (int) $matches[1]);
        }

        $perMonth = round($amount / $months, 2);
        $total = 0.0;

        for ($i = 1; $i <= $months; $i++) {
            $value = $i === $months ? ($amount - $total) : $perMonth;
            $total += $value;

            Repayment::create([
                'project_id' => $project->id,
                'montant_total' => $value,
                'montant_restant' => $value,
                'montant_rembourse' => 0,
                'date_echeance' => Carbon::now()->addMonths($i),
                'statut' => 'en_attente',
            ]);
        }
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|max:100',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        $user = $request->user();
        $project = Project::findOrFail($request->project_id);

        if ($project->user_id !== $user->id) {
            return back()->withErrors(['error' => 'Vous n\'avez pas accès à ce projet.']);
        }

        try {
            $file = $request->file('fichier');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('documents/'.$project->id, $filename, 'local');

            ProjectDocument::create([
                'project_id' => $project->id,
                'type' => $request->type,
                'fichier' => $path,
                'statut_validation' => 'en_attente',
                'uploaded_at' => now(),
            ]);

            return back()->with('success', 'Document uploadé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'upload: '.$e->getMessage()]);
        }
    }

    public function replaceDocument(Request $request, ProjectDocument $document)
    {
        $user = auth()->user();
        if ($document->project->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        try {
            // Supprimer l'ancien fichier
            if (\Storage::disk('local')->exists($document->fichier)) {
                \Storage::disk('local')->delete($document->fichier);
            }

            // Uploader le nouveau
            $file = $request->file('fichier');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('documents/'.$document->project_id, $filename, 'local');

            // Mettre à jour le document
            $document->update([
                'fichier' => $path,
                'statut_validation' => 'en_attente',
                'uploaded_at' => now(),
            ]);

            return back()->with('success', 'Document remplacé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors du remplacement: '.$e->getMessage()]);
        }
    }

    public function deleteDocument(ProjectDocument $document)
    {
        $user = auth()->user();
        if ($document->project->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        try {
            // Supprimer le fichier du stockage
            if (\Storage::disk('local')->exists($document->fichier)) {
                \Storage::disk('local')->delete($document->fichier);
            }

            // Supprimer l'enregistrement
            $document->delete();

            return back()->with('success', 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: '.$e->getMessage()]);
        }
    }

    public function storeProject(Request $request)
    {
        if ($request->user()->role !== 'porteur') {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'secteur' => 'nullable|string|max:255',
            'branche' => 'nullable|string|max:255',
            'montant_demande' => 'required|numeric|min:0',
            'duree' => 'nullable|string|max:255',
            'localisation' => 'nullable|string|max:255',
            'statut' => 'nullable|string|in:draft,published',
        ]);

        $isPublished = ($data['statut'] ?? 'draft') === 'published';
        $statut = $isPublished ? ProjectStatus::SUBMITTED->value : ProjectStatus::DRAFT->value;

        try {
            $project = Project::create([
                'user_id' => $request->user()->id,
                'titre' => $data['titre'],
                'description' => $data['description'] ?? '',
                'secteur' => $data['secteur'] ?? '',
                'branche' => $data['branche'] ?? '',
                'montant_demande' => $data['montant_demande'],
                'duree' => $data['duree'] ?? '',
                'localisation' => $data['localisation'] ?? '',
                'montant_finance' => 0,
                'statut' => $statut,
            ]);

            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'old_status' => ProjectStatus::DRAFT->value,
                'new_status' => $statut,
                'reason' => $isPublished ? 'Projet créé et publié' : 'Projet créé (brouillon)',
                'actor_id' => $request->user()->id,
                'metadata' => ['source' => 'web.projects.store'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Projet créé avec succès.',
                'project' => [
                    'id' => $project->id,
                    'titre' => $project->titre,
                    'statut' => $project->statut,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: '.$e->getMessage()], 500);
        }
    }

    public function uploadProjectDocument(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|max:100',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        $user = $request->user();
        $project = Project::findOrFail($request->project_id);

        if ($project->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        try {
            $file = $request->file('fichier');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('documents/'.$project->id, $filename, 'local');

            ProjectDocument::create([
                'project_id' => $project->id,
                'type' => $request->type,
                'fichier' => $path,
                'statut_validation' => 'en_attente',
                'uploaded_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Document uploadé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: '.$e->getMessage()], 500);
        }
    }
}
