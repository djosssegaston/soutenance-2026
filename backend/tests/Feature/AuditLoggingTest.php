<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuditLoggingTest extends TestCase
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

    public function test_audit_log_can_be_created(): void
    {
        $log = AuditLog::log($this->admin->id, 'Test action', 'bi-test', 'primary', 'info');

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'user_id' => $this->admin->id,
            'action' => 'Test action',
            'type' => 'info',
        ]);

        $this->assertNotNull($log->ip);
    }

    public function test_audit_log_belongs_to_user(): void
    {
        $log = AuditLog::log($this->admin->id, 'User action');

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($this->admin->id, $log->user->id);
    }

    public function test_admin_settings_update_creates_audit_log(): void
    {
        $this->actingAs($this->admin);

        $this->postJson('/api/v1/admin/settings', [
            'platform_name' => 'ALOGOTO Test',
            'commission' => '3.0',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Paramètres de la plateforme mis à jour',
        ]);
    }

    public function test_admin_settings_update_persists(): void
    {
        $this->actingAs($this->admin);

        $this->postJson('/api/v1/admin/settings', [
            'platform_name' => 'ALOGOTO PROD',
            'commission' => '2.5',
            'min_financement' => '50000',
            'max_financement' => '100000000',
            'maintenance_mode' => false,
        ]);

        $this->assertDatabaseHas('settings', ['key' => 'platform_name', 'value' => 'ALOGOTO PROD']);
        $this->assertDatabaseHas('settings', ['key' => 'commission', 'value' => '2.5']);
        $this->assertDatabaseHas('settings', ['key' => 'min_financement', 'value' => '50000']);
        $this->assertDatabaseHas('settings', ['key' => 'max_financement', 'value' => '100000000']);
        $this->assertDatabaseHas('settings', ['key' => 'maintenance_mode', 'value' => '0']);
    }

    public function test_admin_settings_index_returns_defaults(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/settings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'platform_name',
                'contact_email',
                'currency',
                'commission',
                'min_financement',
                'max_financement',
                'maintenance_mode',
                'registration_open',
            ],
        ]);
    }

    public function test_settings_index_returns_custom_values(): void
    {
        Setting::updateOrCreate(['key' => 'platform_name'], ['value' => 'ALOGOTO CUSTOM']);
        Setting::updateOrCreate(['key' => 'commission'], ['value' => '4.0']);

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/settings');

        $response->assertJsonPath('data.platform_name', 'ALOGOTO CUSTOM');
        $response->assertJsonPath('data.commission', '4.0');
    }

    public function test_security_settings_update(): void
    {
        $this->actingAs($this->admin);

        $this->postJson('/api/v1/admin/security', [
            'password_min_length' => 10,
            'max_login_attempts' => 5,
            'session_lifetime' => 60,
            'require_2fa' => true,
        ]);

        $this->assertDatabaseHas('settings', ['key' => 'security_password_min_length', 'value' => '10']);
        $this->assertDatabaseHas('settings', ['key' => 'security_max_login_attempts', 'value' => '5']);
        $this->assertDatabaseHas('settings', ['key' => 'security_session_lifetime', 'value' => '60']);
        $this->assertDatabaseHas('settings', ['key' => 'security_require_2fa', 'value' => '1']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'Politique de sécurité mise à jour',
        ]);
    }

    public function test_audit_logs_index(): void
    {
        AuditLog::log($this->admin->id, 'Action 1', 'bi-1', 'primary', 'info');
        AuditLog::log($this->porteur->id, 'Action 2', 'bi-2', 'secondary', 'warning');

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/audit-logs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total',
            'stats' => ['today', 'validations', 'rejections', 'security'],
            'data',
        ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_audit_logs_search_filter(): void
    {
        AuditLog::log($this->admin->id, 'Validation du projet Test', 'bi-check', 'success', 'info');
        AuditLog::log($this->admin->id, 'Connexion utilisateur', 'bi-person', 'primary', 'info');

        $this->actingAs($this->admin);

        $response = $this->getJson('/api/v1/admin/audit-logs?search=Validation');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Validation du projet Test', $response->json('data.0.action'));
    }
}
