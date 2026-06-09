<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Conversation;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Message;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use App\Models\ProjectValidation;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkflowE2ETest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    private User $porteur;

    private User $institutionUser;

    private Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin-test@alogoto.bj',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $this->porteur = User::factory()->create([
            'name' => 'Porteur Test',
            'email' => 'porteur-test@alogoto.bj',
            'role' => 'porteur',
            'password' => bcrypt('password'),
        ]);

        $this->institutionUser = User::factory()->create([
            'name' => 'Institution Test',
            'email' => 'institution-test@alogoto.bj',
            'role' => 'institution',
            'password' => bcrypt('password'),
        ]);

        $this->institution = Institution::factory()->create([
            'user_id' => $this->institutionUser->id,
            'nom' => 'Banque Test',
        ]);
    }

    public function test_full_workflow_e2e(): void
    {
        // ===== STEP 1: Porteur creates a project =====
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Projet Test E2E',
            'montant_demande' => 5000000,
            'statut' => ProjectStatus::DRAFT->value,
        ]);

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'statut' => ProjectStatus::DRAFT->value]);

        // ===== STEP 2: Porteur submits the project =====
        $project->update(['statut' => ProjectStatus::SUBMITTED->value]);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::DRAFT->value,
            'new_status' => ProjectStatus::SUBMITTED->value,
            'reason' => 'Soumission test',
            'actor_id' => $this->porteur->id,
        ]);

        $this->assertEquals(ProjectStatus::SUBMITTED->value, $project->fresh()->statut);

        // ===== STEP 3: Admin validates the project =====
        $this->actingAs($this->admin);
        $project->update(['statut' => ProjectStatus::ADMIN_VALIDATED->value]);
        ProjectValidation::create([
            'project_id' => $project->id,
            'admin_id' => $this->admin->id,
            'decision' => 'valide',
            'commentaire' => 'Validation test',
        ]);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::SUBMITTED->value,
            'new_status' => ProjectStatus::ADMIN_VALIDATED->value,
            'reason' => 'Validation admin test',
            'actor_id' => $this->admin->id,
        ]);

        $this->assertEquals(ProjectStatus::ADMIN_VALIDATED->value, $project->fresh()->statut);

        // ===== STEP 4: Institution analyzes the project =====
        $this->actingAs($this->institutionUser);
        $project->update(['statut' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value]);
        $analysis = InstitutionAnalysis::create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
            'statut' => 'en_analyse',
            'risk_score' => 65,
        ]);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::ADMIN_VALIDATED->value,
            'new_status' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value,
            'reason' => 'Analyse institution test',
            'actor_id' => $this->institutionUser->id,
        ]);

        $this->assertEquals(ProjectStatus::UNDER_INSTITUTION_REVIEW->value, $project->fresh()->statut);
        $this->assertDatabaseHas('institution_analyses', ['project_id' => $project->id]);

        // ===== STEP 5: Institution accepts =====
        $project->update(['statut' => ProjectStatus::INSTITUTION_ACCEPTED->value]);
        $analysis->update(['statut' => 'accepte']);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value,
            'new_status' => ProjectStatus::INSTITUTION_ACCEPTED->value,
            'reason' => 'Acceptation institution test',
            'actor_id' => $this->institutionUser->id,
        ]);

        $this->assertEquals(ProjectStatus::INSTITUTION_ACCEPTED->value, $project->fresh()->statut);

        // ===== STEP 6: Institution funds the project =====
        $funding = Funding::create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
            'porteur_id' => $this->porteur->id,
            'montant' => $project->montant_demande,
            'montant_demande' => $project->montant_demande,
            'montant_valide' => $project->montant_demande,
            'statut' => 'finance',
            'taux_interet' => 5.0,
            'duree' => 12,
        ]);
        $project->update([
            'statut' => ProjectStatus::FUNDED->value,
            'montant_finance' => $project->montant_demande,
        ]);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::INSTITUTION_ACCEPTED->value,
            'new_status' => ProjectStatus::FUNDED->value,
            'reason' => 'Financement test',
            'actor_id' => $this->institutionUser->id,
        ]);

        $this->assertEquals(ProjectStatus::FUNDED->value, $project->fresh()->statut);
        $this->assertDatabaseHas('financements', ['project_id' => $project->id, 'statut' => 'finance']);

        // ===== STEP 7: Create repayment schedule =====
        $perMonth = $project->montant_demande / 6;
        for ($i = 1; $i <= 6; $i++) {
            Repayment::create([
                'project_id' => $project->id,
                'institution_id' => $this->institution->id,
                'montant_total' => $perMonth,
                'montant_restant' => $perMonth,
                'date_echeance' => now()->addMonths($i),
                'statut' => 'en_attente',
            ]);
        }

        $this->assertEquals(6, Repayment::where('project_id', $project->id)->count());

        // ===== STEP 8: Porteur and Institution exchange messages =====
        $conversation = Conversation::create([
            'project_id' => $project->id,
            'porteur_id' => $this->porteur->id,
            'institution_id' => $this->institution->id,
            'status' => 'active',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->porteur->id,
            'sender_role' => 'porteur',
            'message' => 'Merci pour le financement !',
            'type' => 'text',
            'is_read' => false,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->institutionUser->id,
            'sender_role' => 'institution',
            'message' => 'De rien, bon courage !',
            'type' => 'text',
            'is_read' => false,
        ]);

        $this->assertEquals(2, Message::where('conversation_id', $conversation->id)->count());

        // ===== STEP 9: Institution views remboursements =====
        $remboursements = Repayment::where('institution_id', $this->institution->id)->get();
        $this->assertCount(6, $remboursements);
        $this->assertNotNull($remboursements->first()->niveau_risque);

        // ===== STEP 10: Institution validates a repayment =====
        $repayment = $remboursements->first();
        $repayment->update([
            'statut' => 'paye',
            'montant_rembourse' => $repayment->montant_total,
            'montant_restant' => 0,
            'date_paiement' => now(),
        ]);

        $this->assertEquals('paye', $repayment->fresh()->statut);

        // ===== STEP 11: Admin dashboard endpoints work =====
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/projects');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/echeances');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/remboursements');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/financements');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/financements/statistics');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/users');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/users/statistics');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/admin/messages/conversations');
        $response->assertStatus(200);

        // ===== STEP 12: Institution remboursement detail endpoint =====
        $this->actingAs($this->institutionUser);

        $response = $this->getJson('/api/v1/institution/remboursements/'.$repayment->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'project' => ['titre'], 'montant_total', 'montant_rembourse', 'montant_restant', 'date_echeance',
        ]);

        // ===== STEP 13: Admin user detail and actions =====
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/users/'.$this->porteur->id);
        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Porteur Test');

        $response = $this->postJson('/api/v1/admin/users/'.$this->porteur->id.'/suspend', [
            'motif' => 'Test suspension',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertEquals('suspendu', $this->porteur->fresh()->statut);

        $response = $this->postJson('/api/v1/admin/users/'.$this->porteur->id.'/activate', [
            'motif' => 'Réactivation test',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertEquals('actif', $this->porteur->fresh()->statut);

        // ===== STEP 14: Admin message reply =====
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/messages/'.$conversation->id);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $response = $this->postJson('/api/v1/admin/messages/'.$conversation->id.'/reply', [
            'content' => 'Message de test admin',
        ]);
        $response->assertStatus(200);

        $this->assertEquals(3, Message::where('conversation_id', $conversation->id)->count());

        // ===== STEP 15: Verify final project state =====
        $project->update(['statut' => ProjectStatus::ACTIVE->value]);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::FUNDED->value,
            'new_status' => ProjectStatus::ACTIVE->value,
            'reason' => 'Projet actif',
            'actor_id' => $this->porteur->id,
        ]);

        $project->update(['statut' => ProjectStatus::COMPLETED->value]);
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'old_status' => ProjectStatus::ACTIVE->value,
            'new_status' => ProjectStatus::COMPLETED->value,
            'reason' => 'Remboursement terminé',
            'actor_id' => $this->porteur->id,
        ]);

        $this->assertEquals(ProjectStatus::COMPLETED->value, $project->fresh()->statut);
    }
}
