<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiStabilizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    private User $porteur;

    private User $institutionUser;

    private Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->porteur = User::factory()->create(['role' => 'porteur']);
        $this->institutionUser = User::factory()->create(['role' => 'institution']);
        $this->institution = Institution::factory()->create(['user_id' => $this->institutionUser->id]);
    }

    public function test_porteur_institutions_endpoint(): void
    {
        Institution::factory()->count(3)->create();

        $this->actingAs($this->porteur);

        $response = $this->getJson('/api/v1/porteur/institutions');

        $response->assertStatus(200);
        $response->assertJsonCount(4); // 3 new + 1 from setUp
    }

    public function test_porteur_projects_list_endpoint(): void
    {
        Project::factory()->count(2)->create(['user_id' => $this->porteur->id]);

        $this->actingAs($this->porteur);

        $response = $this->getJson('/api/v1/porteur/projects-list');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['id', 'titre']]]);
    }

    public function test_institution_dashboard_endpoint_returns_summary(): void
    {
        $project = Project::factory()->create(['statut' => ProjectStatus::FUNDED->value]);
        Funding::factory()->create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
        ]);

        $this->actingAs($this->institutionUser);

        $response = $this->getJson('/api/v1/institution/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'summary' => ['portfolio', 'repayments', 'risks', 'pipeline'],
                'recent_activity',
                'alerts' => ['late_repayments', 'high_risk_projects'],
            ],
        ]);
        $response->assertJsonPath('status', 'success');
    }

    public function test_institution_charts_endpoint(): void
    {
        $this->actingAs($this->institutionUser);

        $response = $this->getJson('/api/v1/institution/dashboard/charts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => ['funding_evolution', 'sector_distribution', 'repayment_status', 'risk_portion'],
        ]);
    }

    public function test_institution_projects_endpoint_returns_porteur_and_niveau_risque(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'statut' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value,
        ]);
        InstitutionAnalysis::factory()->create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
            'risk_score' => 75,
        ]);

        $this->actingAs($this->institutionUser);

        $response = $this->getJson('/api/v1/dashboard/institution/projects');

        $response->assertStatus(200);
        $response->assertJsonStructure(['projects' => [['porteur', 'niveau_risque']]]);

        $projects = $response->json('projects');
        $this->assertNotNull($projects[0]['porteur']);
        $this->assertNotNull($projects[0]['niveau_risque']);
    }

    public function test_admin_dashboard_endpoint(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'kpis' => ['projects' => ['total', 'active', 'validated', 'suspended']],
        ]);
        $response->assertJsonPath('success', true);
    }

    public function test_admin_dashboard_charts(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/dashboard/charts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'charts' => ['evolution' => ['labels', 'datasets'], 'sectors', 'status', 'risks'],
        ]);
    }

    public function test_admin_dashboard_activity_and_system(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/dashboard/activity');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'activities']);

        $response = $this->getJson('/api/v1/admin/dashboard/system');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'system' => ['server', 'database', 'backups', 'api']]);
    }

    public function test_project_creation_includes_branche(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'branche' => 'Agriculture',
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'branche' => 'Agriculture',
        ]);
        $this->assertContains('branche', $project->getFillable());
    }

    public function test_repayment_schedule_uses_montant_total(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        $repayment = Repayment::create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
            'montant_total' => 100000,
            'montant_restant' => 100000,
            'montant_rembourse' => 0,
            'date_echeance' => now()->addMonth(),
            'statut' => 'en_attente',
        ]);

        $this->assertDatabaseHas('remboursements', [
            'id' => $repayment->id,
            'montant_total' => 100000,
            'montant_restant' => 100000,
            'montant_rembourse' => 0,
        ]);
    }

    public function test_institution_project_controller_uses_institution_analysis(): void
    {
        $project = Project::factory()->create([
            'statut' => ProjectStatus::ADMIN_VALIDATED->value,
            'user_id' => $this->porteur->id,
        ]);

        $this->actingAs($this->institutionUser);

        $response = $this->postJson('/api/v1/projects/'.$project->id.'/institution-analyze', [
            'risk_score' => 80,
            'commentaire' => 'Test analyse',
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_project_controller_without_risk_tables(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/projects/'.$project->id);

        $response->assertStatus(200);
    }
}
