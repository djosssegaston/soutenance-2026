<?php

namespace Tests\Feature;

use App\Models\DocumentRule;
use App\Models\Secteur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecteurDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $porteur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->porteur = User::factory()->create(['role' => 'porteur']);
    }

    // ─── SECTEUR CRUD ───

    public function test_admin_can_create_secteur(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/secteurs', [
            'nom' => 'Agriculture',
            'description' => 'Secteur agricole',
            'icone' => 'fe fe-leaf',
            'is_active' => true,
            'order_column' => 1,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Secteur créé.']);
        $this->assertDatabaseHas('secteurs', ['nom' => 'Agriculture']);
    }

    public function test_admin_cannot_create_duplicate_secteur(): void
    {
        Secteur::create(['nom' => 'Agriculture']);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/secteurs', [
            'nom' => 'Agriculture',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_secteur(): void
    {
        $secteur = Secteur::create(['nom' => 'Ancien Nom']);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/secteurs/'.$secteur->id, [
            'nom' => 'Nouveau Nom',
            'is_active' => true,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('secteurs', ['nom' => 'Nouveau Nom']);
    }

    public function test_admin_can_delete_secteur(): void
    {
        $secteur = Secteur::create(['nom' => 'Test']);

        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/secteurs/'.$secteur->id);

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Secteur supprimé.']);
        $this->assertDatabaseMissing('secteurs', ['id' => $secteur->id]);
    }

    public function test_admin_can_toggle_secteur_status(): void
    {
        $secteur = Secteur::create(['nom' => 'Test', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/secteurs/'.$secteur->id, [
            'nom' => 'Test',
            'is_active' => false,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('secteurs', ['id' => $secteur->id, 'is_active' => false]);
    }

    public function test_non_admin_cannot_manage_secteurs(): void
    {
        $response = $this->actingAs($this->porteur)->postJson('/api/v1/admin/secteurs', [
            'nom' => 'Test',
        ]);

        $response->assertStatus(403);
    }

    // ─── DOCUMENT RULE CRUD ───

    public function test_admin_can_create_document_rule(): void
    {
        $secteur = Secteur::create(['nom' => 'Agriculture']);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'label' => 'Pièce d\'identité',
            'slug' => 'piece_identite',
            'secteur_id' => $secteur->id,
            'obligatoire' => true,
            'acteur' => 'porteur',
            'types_mime' => 'pdf,jpg,png',
            'max_size' => 10,
            'description' => 'Copie de la pièce d\'identité',
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('document_rules', ['slug' => 'piece_identite']);
    }

    public function test_admin_cannot_create_duplicate_document_rule_slug(): void
    {
        DocumentRule::create([
            'label' => 'Pièce d\'identité',
            'slug' => 'piece_identite',
            'acteur' => 'porteur',
        ]);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'label' => 'Autre pièce',
            'slug' => 'piece_identite',
            'acteur' => 'porteur',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_document_rule(): void
    {
        $rule = DocumentRule::create([
            'label' => 'Ancien',
            'slug' => 'ancien_slug',
            'acteur' => 'porteur',
        ]);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/document-rules/'.$rule->id, [
            'label' => 'Mis à jour',
            'slug' => 'ancien_slug',
            'acteur' => 'porteur',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('document_rules', ['label' => 'Mis à jour']);
    }

    public function test_admin_can_delete_document_rule(): void
    {
        $rule = DocumentRule::create([
            'label' => 'Test',
            'slug' => 'test_doc',
            'acteur' => 'porteur',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/document-rules/'.$rule->id);

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Règle de document supprimée.']);
        $this->assertDatabaseMissing('document_rules', ['id' => $rule->id]);
    }

    public function test_admin_can_toggle_document_rule_status(): void
    {
        $rule = DocumentRule::create([
            'label' => 'Test',
            'slug' => 'test_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/document-rules/'.$rule->id, [
            'label' => 'Test',
            'slug' => 'test_doc',
            'acteur' => 'porteur',
            'is_active' => false,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('document_rules', ['id' => $rule->id, 'is_active' => false]);
    }

    public function test_validation_required_label(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'label' => '',
            'slug' => 'test',
            'acteur' => 'porteur',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['label']);
    }

    public function test_validation_max_size_bounds(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'label' => 'Test',
            'slug' => 'test',
            'acteur' => 'porteur',
            'max_size' => 999999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_size']);
    }

    // ─── PORTEUR ACCESS ───

    public function test_porteur_can_list_active_secteurs(): void
    {
        Secteur::create(['nom' => 'Agriculture', 'is_active' => true]);
        Secteur::create(['nom' => 'Inactif', 'is_active' => false]);

        $response = $this->actingAs($this->porteur)->getJson('/api/secteurs/active');

        $response->assertOk()
            ->assertJsonFragment(['nom' => 'Agriculture'])
            ->assertJsonMissing(['nom' => 'Inactif']);
    }

    public function test_porteur_can_list_active_document_rules(): void
    {
        DocumentRule::create(['label' => 'Doc1', 'slug' => 'doc1', 'acteur' => 'porteur', 'is_active' => true]);
        DocumentRule::create(['label' => 'Doc2', 'slug' => 'doc2', 'acteur' => 'porteur', 'is_active' => false]);

        $response = $this->actingAs($this->porteur)->getJson('/api/document-rules/porteur');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'doc1'])
            ->assertJsonMissing(['slug' => 'doc2']);
    }

    public function test_document_rules_are_scoped_to_porteur_role(): void
    {
        DocumentRule::create(['label' => 'Porteur Doc', 'slug' => 'porteur_doc', 'acteur' => 'porteur', 'is_active' => true]);
        DocumentRule::create(['label' => 'Admin Doc', 'slug' => 'admin_doc', 'acteur' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($this->porteur)->getJson('/api/document-rules/porteur');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'porteur_doc'])
            ->assertJsonMissing(['slug' => 'admin_doc']);
    }

    public function test_non_auth_cannot_access_secteurs(): void
    {
        $response = $this->getJson('/api/secteurs/active');
        $response->assertStatus(401);
    }

    public function test_non_admin_cannot_create_secteur(): void
    {
        $response = $this->actingAs($this->porteur)->postJson('/api/v1/admin/secteurs', [
            'nom' => 'Test',
        ]);
        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_create_document_rule(): void
    {
        $response = $this->actingAs($this->porteur)->postJson('/api/v1/admin/document-rules', [
            'label' => 'Test',
            'slug' => 'test',
            'acteur' => 'porteur',
        ]);
        $response->assertStatus(403);
    }
}
