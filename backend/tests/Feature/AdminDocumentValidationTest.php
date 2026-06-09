<?php

namespace Tests\Feature;

use App\Models\DocumentRule;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminDocumentValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    private User $porteur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->porteur = User::factory()->create(['role' => 'porteur']);
    }

    public function test_documents_index(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        ProjectDocument::factory()->count(3)->create([
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/documents');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total',
            'stats' => ['total', 'pending', 'validated', 'rejected'],
            'data',
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_validate_document(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);
        $document = ProjectDocument::factory()->create([
            'project_id' => $project->id,
            'statut_validation' => 'en_attente',
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson('/api/v1/admin/documents/'.$document->id.'/validate');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('project_documents', [
            'id' => $document->id,
            'statut_validation' => 'valide',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Document validé: '.$document->type.' (projet #'.$project->id.')',
        ]);
    }

    public function test_reject_document(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);
        $document = ProjectDocument::factory()->create([
            'project_id' => $project->id,
            'statut_validation' => 'en_attente',
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson('/api/v1/admin/documents/'.$document->id.'/reject', [
            'motif' => 'Document incomplet',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('project_documents', [
            'id' => $document->id,
            'statut_validation' => 'rejete',
        ]);
    }

    public function test_reject_document_requires_motif(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);
        $document = ProjectDocument::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson('/api/v1/admin/documents/'.$document->id.'/reject', []);

        $response->assertStatus(422);
    }

    public function test_document_checklists(): void
    {
        DocumentRule::factory()->count(3)->create([
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/documents/checklists');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_project_checklist(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        $rule = DocumentRule::factory()->create([
            'slug' => 'cni',
            'is_active' => true,
            'acteur' => 'porteur',
        ]);

        ProjectDocument::factory()->create([
            'project_id' => $project->id,
            'type' => 'cni',
            'statut_validation' => 'valide',
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/documents/project/'.$project->id.'/checklist');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'project',
            'stats' => ['total', 'obligatoire', 'uploaded', 'completion'],
            'checklist',
        ]);
    }

    public function test_documents_filter_by_status(): void
    {
        $project = Project::factory()->create(['user_id' => $this->porteur->id]);

        ProjectDocument::factory()->create([
            'project_id' => $project->id,
            'statut_validation' => 'valide',
        ]);

        ProjectDocument::factory()->create([
            'project_id' => $project->id,
            'statut_validation' => 'en_attente',
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/documents?status=valide');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_documents_unauthorized(): void
    {
        $this->actingAs($this->porteur);

        $this->getJson('/api/v1/admin/documents')->assertStatus(403);
    }
}
