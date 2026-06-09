<?php

namespace Tests\Feature;

use App\Enums\EcheanceStatus;
use App\Enums\FundingStatus;
use App\Enums\ProjectStatus;
use App\Models\Echeance;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use App\Models\User;
use App\Services\EcheanceService;
use App\Services\FinancingWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancingWorkflowE2ETest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $porteur;

    private User $institutionUser;

    private Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.bj',
            'role' => 'admin',
        ]);

        $this->porteur = User::factory()->create([
            'name' => 'Porteur',
            'email' => 'porteur@test.bj',
            'role' => 'porteur',
        ]);

        $this->institutionUser = User::factory()->create([
            'name' => 'IMF',
            'email' => 'imf@test.bj',
            'role' => 'institution',
        ]);

        $this->institution = Institution::factory()->create([
            'user_id' => $this->institutionUser->id,
            'nom' => 'IMF Test',
        ]);
    }

    public function test_complete_financing_workflow(): void
    {
        $workflow = app(FinancingWorkflowService::class);
        $echeanceService = app(EcheanceService::class);

        // ===== STEP 1: Create project =====
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Projet Test Financement',
            'montant_demande' => 5000000,
            'statut' => ProjectStatus::DRAFT->value,
        ]);

        $this->assertEquals(ProjectStatus::DRAFT->value, $project->fresh()->statut);

        // ===== STEP 2: Submit project =====
        $project->update(['statut' => ProjectStatus::SUBMITTED->value]);

        $this->assertEquals(ProjectStatus::SUBMITTED->value, $project->fresh()->statut);

        // ===== STEP 3: Admin validates project =====
        $project->update(['statut' => ProjectStatus::ADMIN_VALIDATED->value]);

        $this->assertEquals(ProjectStatus::ADMIN_VALIDATED->value, $project->fresh()->statut);

        // ===== STEP 4: Make project available for IMF =====
        $project->update(['statut' => ProjectStatus::AVAILABLE_FOR_IMF->value]);

        $this->assertEquals(ProjectStatus::AVAILABLE_FOR_IMF->value, $project->fresh()->statut);

        // ===== STEP 5: IMF makes a financing proposal =====
        $this->assertTrue($project->peutEtreFinance());

        $funding = $workflow->imfFaireProposition($this->institutionUser, $project, [
            'montant_propose' => 5000000,
            'taux_interet' => 8.5,
            'duree' => 12,
            'conditions' => 'Remboursement mensuel',
            'frais' => 'Frais de dossier: 1%',
            'commentaires' => 'Offre valable 30 jours',
        ]);

        $this->assertEquals(FundingStatus::AWAITING_BORROWER_PLAN->value, $funding->statut);
        $this->assertDatabaseHas('financements', [
            'id' => $funding->id,
            'statut' => FundingStatus::AWAITING_BORROWER_PLAN->value,
            'montant_propose' => 5000000,
            'taux_interet' => 8.50,
            'duree' => 12,
        ]);

        // ===== STEP 6: Porteur submits repayment plan =====
        $funding = $workflow->porteurSoumettrePlan($this->porteur, $funding, [
            'montant_mensuel' => 450000,
            'jour_remboursement' => 9,
            'commentaire' => 'Je préfère le 9 de chaque mois',
        ]);

        $this->assertEquals(FundingStatus::AWAITING_IMF_VALIDATION->value, $funding->statut);
        $this->assertEquals(450000, (float) $funding->montant_mensuel);
        $this->assertEquals(9, (int) $funding->jour_remboursement);

        // ===== STEP 7: IMF approves the plan =====
        $funding = $workflow->imfApprouverPlan($this->institutionUser, $funding);

        $this->assertEquals(FundingStatus::DISBURSED->value, $funding->statut);
        $this->assertNotNull($funding->date_approbation_imf);
        $this->assertNotNull($funding->date_decaissement);

        // ===== STEP 8: Echeances were auto-generated =====
        $echeances = Echeance::where('financement_id', $funding->id)
            ->orderBy('numero_echeance')
            ->get();

        $this->assertGreaterThan(0, $echeances->count());
        // Number of echeances depends on montant_mensuel chosen by porteur
        $this->assertLessThanOrEqual(13, $echeances->count());

        // Verify echeance dates start AFTER disbursement
        $firstEcheance = $echeances->first();
        $this->assertGreaterThan(
            $funding->date_decaissement,
            $firstEcheance->date_echeance
        );

        // Verify total of all echeances covers capital + interest
        $totalEcheances = (float) $echeances->sum('montant_total');
        $this->assertGreaterThanOrEqual(5000000, $totalEcheances);

        // Verify progression
        $progression = $echeanceService->getProgression($funding);
        $this->assertEquals(0, $progression['pourcentage']);
        $this->assertEquals($echeances->count(), $progression['total_echeances']);

        // ===== STEP 9: Verify funding uniqueness =====
        $this->assertFalse($project->fresh()->peutEtreFinance());
        $this->assertTrue($project->fresh()->aUnFinancementActif());

        // ===== STEP 10: Make a payment on an echeance =====
        $echeance = $echeances->first();
        $montantEcheance = (float) $echeance->montant_total;

        $echeanceService->enregistrerPaiement($echeance, $montantEcheance, 'manuel', 'TXN-TEST-001');

        $echeance = $echeance->fresh();
        $this->assertEquals(EcheanceStatus::PAID->value, $echeance->statut);
        $this->assertEquals($montantEcheance, (float) $echeance->montant_paye);
        $this->assertEquals(0, (float) $echeance->montant_restant);

        // ===== STEP 11: Pay remaining echeances =====
        $remainingEcheances = Echeance::where('financement_id', $funding->id)
            ->where('statut', '!=', EcheanceStatus::PAID->value)
            ->get();

        foreach ($remainingEcheances as $ech) {
            $echeanceService->enregistrerPaiement($ech, (float) $ech->montant_total, 'manuel');
        }

        // ===== STEP 12: Verify funding is completed =====
        $funding = $funding->fresh();
        $this->assertEquals(FundingStatus::COMPLETED->value, $funding->statut);
        $this->assertNotNull($funding->date_cloture);

        // Verify project is available for IMF again (repaid → closed → available)
        $project = $project->fresh();
        $this->assertEquals(ProjectStatus::AVAILABLE_FOR_IMF->value, $project->statut);

        // ===== STEP 13: Project can be refinanced =====
        $this->assertTrue($project->peutEtreFinance());
        $this->assertFalse($project->aUnFinancementActif());
    }

    public function test_cannot_refinance_active_project(): void
    {
        $workflow = app(FinancingWorkflowService::class);

        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Test Refus Surfinancement',
            'montant_demande' => 10000000,
            'statut' => ProjectStatus::AVAILABLE_FOR_IMF->value,
        ]);

        // First financing
        $funding1 = $workflow->imfFaireProposition($this->institutionUser, $project, [
            'montant_propose' => 5000000,
            'taux_interet' => 8.0,
            'duree' => 12,
        ]);

        $workflow->porteurSoumettrePlan($this->porteur, $funding1, [
            'montant_mensuel' => 450000,
            'jour_remboursement' => 5,
        ]);

        $workflow->imfApprouverPlan($this->institutionUser, $funding1);

        // Try to create another financing on same project
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('proposition de financement active');

        $workflow->imfFaireProposition($this->institutionUser, $project, [
            'montant_propose' => 3000000,
            'taux_interet' => 7.5,
            'duree' => 6,
        ]);
    }

    public function test_imf_can_reject_porteur_plan(): void
    {
        $workflow = app(FinancingWorkflowService::class);

        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Test Rejet Plan',
            'montant_demande' => 5000000,
            'statut' => ProjectStatus::AVAILABLE_FOR_IMF->value,
        ]);

        $funding = $workflow->imfFaireProposition($this->institutionUser, $project, [
            'montant_propose' => 5000000,
            'taux_interet' => 8.5,
            'duree' => 12,
        ]);

        $workflow->porteurSoumettrePlan($this->porteur, $funding, [
            'montant_mensuel' => 450000,
            'jour_remboursement' => 9,
        ]);

        $funding = $workflow->imfRejeterPlan($this->institutionUser, $funding, 'Montant mensuel trop bas');

        $this->assertEquals(FundingStatus::REJECTED->value, $funding->statut);
        $this->assertEquals('Montant mensuel trop bas', $funding->motif_rejet_imf);
        $this->assertNotNull($funding->date_rejet_imf);
    }

    public function test_imf_can_request_revision(): void
    {
        $workflow = app(FinancingWorkflowService::class);

        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Test Revision',
            'montant_demande' => 5000000,
            'statut' => ProjectStatus::AVAILABLE_FOR_IMF->value,
        ]);

        $funding = $workflow->imfFaireProposition($this->institutionUser, $project, [
            'montant_propose' => 5000000,
            'taux_interet' => 8.5,
            'duree' => 12,
        ]);

        $workflow->porteurSoumettrePlan($this->porteur, $funding, [
            'montant_mensuel' => 450000,
            'jour_remboursement' => 9,
        ]);

        $funding = $workflow->imfDemanderRevision($this->institutionUser, $funding, 'Veuillez augmenter le montant mensuel');

        $this->assertEquals(FundingStatus::AWAITING_BORROWER_PLAN->value, $funding->statut);

        // Porteur resubmits
        $funding = $workflow->porteurSoumettrePlan($this->porteur, $funding, [
            'montant_mensuel' => 500000,
            'jour_remboursement' => 9,
            'commentaire' => 'Nouveau plan avec montant augmenté',
        ]);

        $this->assertEquals(FundingStatus::AWAITING_IMF_VALIDATION->value, $funding->statut);
        $this->assertEquals(500000, (float) $funding->montant_mensuel);
    }
}
