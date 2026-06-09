<?php

namespace Tests\Feature;

use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RepaymentReceiptTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $porteur;

    private User $institution;

    private User $admin;

    private Project $project;

    private Repayment $repayment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->porteur = User::factory()->create(['role' => 'porteur']);
        $this->institution = User::factory()->create(['role' => 'institution']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        Institution::factory()->create([
            'user_id' => $this->institution->id,
        ]);

        $this->project = Project::factory()->create([
            'user_id' => $this->porteur->id,
        ]);

        $this->repayment = Repayment::factory()->create([
            'project_id' => $this->project->id,
        ]);
    }

    public function test_generate_receipt(): void
    {
        $this->actingAs($this->porteur);

        $response = $this->getJson('/api/v1/repayments/receipt/'.$this->repayment->id);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['receipt' => ['receipt_number', 'repayment', 'project']]);
    }

    public function test_admin_can_generate_any_receipt(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/repayments/receipt/'.$this->repayment->id);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_simulate_repayment(): void
    {
        $this->actingAs($this->porteur);

        $response = $this->postJson('/api/v1/repayments/simulate', [
            'montant' => 100000,
            'taux_interet' => 12,
            'duree_mois' => 12,
            'date_debut' => '2026-01-01',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['simulation' => ['montant', 'taux_annuel', 'duree_mois', 'mensualite', 'total_rembourse', 'total_interets', 'taeg', 'echeancier']]);

        $echeances = $response->json('simulation.echeancier');
        $this->assertCount(12, $echeances);
    }

    public function test_simulate_repayment_validation(): void
    {
        $this->actingAs($this->porteur);

        $response = $this->postJson('/api/v1/repayments/simulate', [
            'montant' => -100,
        ]);

        $response->assertStatus(422);
    }

    public function test_porteur_late_repayments(): void
    {
        Repayment::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'statut' => 'en_retard',
        ]);

        Repayment::factory()->create([
            'project_id' => $this->project->id,
            'statut' => 'paye',
        ]);

        $this->actingAs($this->porteur);

        $response = $this->getJson('/api/v1/repayments/late/porteur');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_institution_late_repayments(): void
    {
        Funding::factory()->create([
            'institution_id' => $this->institution->institution->id,
            'project_id' => $this->project->id,
        ]);

        Repayment::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'statut' => 'en_retard',
        ]);

        $this->actingAs($this->institution);

        $response = $this->getJson('/api/v1/repayments/late/institution');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_institution_late_repayments_unauthorized_for_porteur(): void
    {
        $this->actingAs($this->porteur);

        $response = $this->getJson('/api/v1/repayments/late/institution');

        $response->assertStatus(403);
    }

    public function test_receipt_not_found(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/repayments/receipt/99999');

        $response->assertStatus(404);
    }
}
