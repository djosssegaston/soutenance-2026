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

class AdminExportTest extends TestCase
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

    public function test_export_projects_csv(): void
    {
        Project::factory()->count(3)->create(['user_id' => $this->porteur->id]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/export/csv?type=projects');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="projets_'.now()->format('Y-m-d').'.csv"');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Titre', $content);
        $this->assertStringContainsString('Montant Demand', $content);
    }

    public function test_export_users_csv(): void
    {
        User::factory()->count(2)->create();

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/export/csv?type=users');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Nom', $content);
        $this->assertStringContainsString('Email', $content);
    }

    public function test_export_repayments_csv(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        Repayment::factory()->count(2)->create([
            'project_id' => $project->id,
            'date_echeance' => Carbon::now(),
            'date_paiement' => Carbon::now(),
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/export/csv?type=repayments');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Projet', $content);
        $this->assertStringContainsString('Montant Total', $content);
    }

    public function test_export_fundings_csv(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        Funding::factory()->create([
            'project_id' => $project->id,
            'institution_id' => $this->institution->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/export/csv?type=fundings');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_export_json(): void
    {
        Project::factory()->count(2)->create(['user_id' => $this->porteur->id]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/export/json?type=projects');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'export_type',
            'exported_at',
            'columns',
            'data',
        ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_export_creates_audit_log(): void
    {
        Project::factory()->create(['user_id' => $this->porteur->id]);

        $this->actingAs($this->admin);
        $this->getJson('/api/v1/admin/export/csv?type=projects');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Export CSV: projects',
        ]);
    }

    public function test_export_with_filters(): void
    {
        Project::factory()->create([
            'user_id' => $this->porteur->id,
            'statut' => 'funded',
        ]);

        Project::factory()->create([
            'user_id' => $this->porteur->id,
            'statut' => 'draft',
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/export/json?type=projects&statut=funded');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_export_unauthorized(): void
    {
        $this->actingAs($this->porteur);

        $this->getJson('/api/v1/admin/export/csv?type=projects')->assertStatus(403);
    }
}
