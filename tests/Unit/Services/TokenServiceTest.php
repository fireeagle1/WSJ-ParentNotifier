<?php

namespace Tests\Unit\Services;

use App\Models\Guardian;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    use RefreshDatabase;

    private TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new TokenService();
    }

    public function test_generate_produces_64_character_token(): void
    {
        $token = $this->tokenService->generate();

        $this->assertSame(64, strlen($token));
    }

    public function test_generate_produces_url_safe_alphanumeric_token(): void
    {
        $token = $this->tokenService->generate();

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $token);
    }

    public function test_generate_produces_unique_tokens(): void
    {
        $tokens = [];
        for ($i = 0; $i < 10; $i++) {
            $tokens[] = $this->tokenService->generate();
        }

        $this->assertCount(10, array_unique($tokens));
    }

    public function test_validate_returns_guardian_for_active_token(): void
    {
        $guardian = Guardian::factory()->create(['access_token' => 'valid_token_123456789012345678901234567890123456789012345678']);

        $result = $this->tokenService->validate($guardian->access_token);

        $this->assertNotNull($result);
        $this->assertTrue($result->is($guardian));
    }

    public function test_validate_returns_null_for_nonexistent_token(): void
    {
        $result = $this->tokenService->validate('nonexistent_token');

        $this->assertNull($result);
    }

    public function test_validate_returns_null_for_revoked_token(): void
    {
        $guardian = Guardian::factory()->revoked()->create();

        $result = $this->tokenService->validate($guardian->access_token);

        $this->assertNull($result);
    }

    public function test_revoke_sets_is_revoked_to_true(): void
    {
        $guardian = Guardian::factory()->create(['is_revoked' => false]);

        $this->tokenService->revoke($guardian);

        $guardian->refresh();
        $this->assertTrue($guardian->is_revoked);
    }

    public function test_revoke_makes_token_invalid_for_validation(): void
    {
        $guardian = Guardian::factory()->create(['is_revoked' => false]);
        $token = $guardian->access_token;

        $this->tokenService->revoke($guardian);

        $result = $this->tokenService->validate($token);
        $this->assertNull($result);
    }
}
