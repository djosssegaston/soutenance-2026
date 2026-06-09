<?php

namespace Tests\Feature;

use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminFinanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    private User $porteur;

    private Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->porteur = User::factory()->create(['role' => 'porteur']);

        $institutionUser = User::factory()->create(['role' => 'institution']);
        $this->institution = Institution::factory()->create(['user_id' => $institutionUser->id]);
    }

    public function test_finance_overview_returns_expected_structure(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'montant_demande' => 10000000,
        ]);

        Funding::factory()->create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
            'montant' => 5000000,
            'date_financement' => Carbon::now(),
        ]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'montant_total' => 1000000,
            'montant_rembourse' => 500000,
            'statut' => 'partiel',
            'date_echeance' => Carbon::now(),
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/finance/overview');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'totals' => ['total_funding', 'total_repaid', 'total_due', 'outstanding', 'total_projects', 'funded_projects'],
                'rates' => ['repayment_rate', 'par30'],
                'monthly_evolution',
                'repayment_detail' => ['total_institutions', 'total_projects_funded', 'average_per_project', 'total_transactions', 'recovery_rate', 'par30'],
            ],
        ]);

        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.totals.total_funding', 5000000);
    }

    public function test_finance_overview_creates_audit_log(): void
    {
        $this->actingAs($this->admin);
        $this->getJson('/api/v1/admin/finance/overview');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Consultation des données financières',
        ]);
    }

    public function test_repayment_table_with_filters(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Test Filter Project',
        ]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'statut' => 'paye',
            'montant_total' => 500000,
            'montant_rembourse' => 500000,
        ]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'statut' => 'en_retard',
            'montant_total' => 300000,
            'montant_rembourse' => 0,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/remboursements/table?status=paye');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'total', 'per_page', 'current_page', 'last_page',
            'stats' => ['total_repayments', 'total_paid', 'total_pending', 'overdue_count'],
            'data',
        ]);

        $response = $this->getJson('/api/v1/admin/remboursements/table?status=en_retard');
        $response->assertJsonPath('stats.overdue_count', 1);
    }

    public function test_funding_table(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        Funding::factory()->create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
            'montant' => 10000000,
            'statut' => 'active',
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/finance/fundings');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'total', 'per_page', 'current_page', 'last_page',
            'stats' => ['total_volume', 'active_count', 'pending_count'],
            'data',
        ]);

        $response->assertJsonPath('stats.total_volume', 10000000);
        $response->assertJsonPath('stats.active_count', 1);
    }

    public function test_funding_table_search(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->porteur->id,
            'titre' => 'Projet Recherche',
        ]);

        Funding::factory()->create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/finance/fundings?search=Projet');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_repayment_table_sorting(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'montant_total' => 100000,
            'statut' => 'paye',
        ]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'montant_total' => 500000,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/remboursements/table?sort=montant_total&dir=desc&per_page=10');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertGreaterThanOrEqual($data[1]['montant_total'], $data[0]['montant_total']);
    }

    public function test_par30_calculation(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'montant_total' => 1000000,
            'montant_rembourse' => 0,
            'statut' => 'en_retard',
            'date_echeance' => Carbon::now()->subDays(45),
        ]);

        Repayment::factory()->create([
            'project_id' => $project->id,
            'montant_total' => 2000000,
            'montant_rembourse' => 0,
            'statut' => 'en_attente',
            'date_echeance' => Carbon::now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/finance/overview');

        $response->assertStatus(200);
        $par30 = $response->json('data.rates.par30');
        $this->assertIsFloat($par30);
        $this->assertGreaterThan(0, $par30);
    }

    public function test_unauthorized_access(): void
    {
        $this->actingAs($this->porteur);

        $response = $this->getJson('/api/v1/admin/finance/overview');
        $response->assertStatus(403);

        $response = $this->getJson('/api/v1/admin/remboursements/table');
        $response->assertStatus(403);
    }
}
