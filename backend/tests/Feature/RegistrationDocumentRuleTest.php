<?php

namespace Tests\Feature;

use App\Models\DocumentRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationDocumentRuleTest extends TestCase
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

    public function test_admin_can_create_registration_document_rule(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'context' => 'registration',
            'label' => 'RCCM',
            'slug' => 'rccm',
            'obligatoire' => true,
            'acteur' => 'institution',
            'types_mime' => 'pdf,jpg,png',
            'max_size' => 10,
            'description' => 'Registre du commerce',
            'is_active' => true,
            'order_column' => 1,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('document_rules', [
            'slug' => 'rccm',
            'context' => 'registration',
            'acteur' => 'institution',
        ]);
    }

    public function test_admin_can_create_project_document_rule_with_default_context(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'label' => 'Pièce d\'identité',
            'slug' => 'piece_identite_default',
            'acteur' => 'porteur',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('document_rules', [
            'slug' => 'piece_identite_default',
            'context' => 'project',
        ]);
    }

    public function test_admin_can_filter_document_rules_by_context(): void
    {
        DocumentRule::create([
            'context' => 'project',
            'label' => 'Projet Doc',
            'slug' => 'projet_doc',
            'acteur' => 'porteur',
        ]);
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Reg Doc',
            'slug' => 'reg_doc',
            'acteur' => 'porteur',
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/document-rules?context=registration');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'reg_doc'])
            ->assertJsonMissing(['slug' => 'projet_doc']);
    }

    public function test_public_api_returns_registration_document_rules(): void
    {
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Reg Doc',
            'slug' => 'reg_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Institution Doc',
            'slug' => 'inst_doc',
            'acteur' => 'institution',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->porteur)->getJson('/api/document-rules/registration?acteur=porteur');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'reg_doc'])
            ->assertJsonMissing(['slug' => 'inst_doc']);
    }

    public function test_registration_document_rules_exclude_inactive(): void
    {
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Active',
            'slug' => 'active_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Inactive',
            'slug' => 'inactive_doc',
            'acteur' => 'porteur',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->porteur)->getJson('/api/document-rules/registration?acteur=porteur');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'active_doc'])
            ->assertJsonMissing(['slug' => 'inactive_doc']);
    }

    public function test_registration_document_rules_are_scoped_separately_from_project_rules(): void
    {
        DocumentRule::create([
            'context' => 'project',
            'label' => 'Project Doc',
            'slug' => 'project_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Registration Doc',
            'slug' => 'registration_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->porteur)->getJson('/api/document-rules/registration?acteur=porteur');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'registration_doc'])
            ->assertJsonMissing(['slug' => 'project_doc']);
    }

    public function test_existing_porteur_route_still_returns_project_rules(): void
    {
        DocumentRule::create([
            'context' => 'project',
            'label' => 'Project Doc',
            'slug' => 'project_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);
        DocumentRule::create([
            'context' => 'registration',
            'label' => 'Registration Doc',
            'slug' => 'registration_doc',
            'acteur' => 'porteur',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->porteur)->getJson('/api/document-rules/porteur');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'project_doc'])
            ->assertJsonMissing(['slug' => 'registration_doc']);
    }

    public function test_non_admin_cannot_manage_registration_document_rules(): void
    {
        $response = $this->actingAs($this->porteur)->postJson('/api/v1/admin/document-rules', [
            'context' => 'registration',
            'label' => 'Test',
            'slug' => 'test',
            'acteur' => 'porteur',
        ]);

        $response->assertStatus(403);
    }

    public function test_validation_invalid_context(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/document-rules', [
            'context' => 'invalid_context',
            'label' => 'Test',
            'slug' => 'test',
            'acteur' => 'porteur',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['context']);
    }
}
