<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'porteur',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_2fa_status_when_disabled(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v1/auth/2fa/status');

        $response->assertStatus(200);
        $response->assertJsonPath('data.enabled', false);
    }

    public function test_2fa_enable(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/auth/2fa/enable');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['data' => ['secret', 'qr_url', 'backup_codes']]);

        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_secret);
        $this->assertNotNull($this->user->two_factor_backup_codes);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => '2FA activé',
        ]);
    }

    public function test_2fa_enable_when_already_enabled(): void
    {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/auth/2fa/enable');

        $response = $this->postJson('/api/v1/auth/2fa/enable');

        $response->assertStatus(422);
    }

    public function test_2fa_disable(): void
    {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/auth/2fa/enable');

        $response = $this->postJson('/api/v1/auth/2fa/disable', [
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->user->refresh();
        $this->assertNull($this->user->two_factor_secret);
        $this->assertNull($this->user->two_factor_backup_codes);
        $this->assertNull($this->user->two_factor_confirmed_at);
    }

    public function test_2fa_disable_wrong_password(): void
    {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/auth/2fa/enable');

        $response = $this->postJson('/api/v1/auth/2fa/disable', [
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_2fa_status_when_enabled(): void
    {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/auth/2fa/enable');

        $response = $this->getJson('/api/v1/auth/2fa/status');

        $response->assertStatus(200);
        $response->assertJsonPath('data.enabled', true);
        $response->assertJsonPath('data.methods', ['app']);
    }

    public function test_2fa_verify_invalid_code(): void
    {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/auth/2fa/enable');

        $response = $this->postJson('/api/v1/auth/2fa/verify', [
            'code' => '000000',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }

    public function test_2fa_recovery_codes(): void
    {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/auth/2fa/enable');

        $response = $this->postJson('/api/v1/auth/2fa/recovery-codes', [
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertCount(8, $response->json('data.backup_codes'));
    }

    public function test_2fa_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/auth/2fa/status');
        $response->assertStatus(401);
    }

    public function test_generates_valid_backup_codes(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/auth/2fa/enable');

        $codes = $response->json('data.backup_codes');
        $this->assertCount(8, $codes);

        foreach ($codes as $code) {
            $this->assertEquals(8, strlen($code));
            $this->assertMatchesRegularExpression('/^[A-F0-9]+$/', $code);
        }
    }

    public function test_disable_without_enabling_returns_error(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/auth/2fa/disable', [
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }
}
