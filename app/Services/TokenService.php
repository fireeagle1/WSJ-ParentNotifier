<?php

namespace App\Services;

use App\Models\Guardian;
use Illuminate\Support\Str;

class TokenService
{
    /**
     * Generate a 64-character URL-safe alphanumeric token.
     */
    public function generate(): string
    {
        return Str::random(64);
    }

    /**
     * Validate a token and return the Guardian model if active, or null otherwise.
     */
    public function validate(string $token): ?Guardian
    {
        return Guardian::where('access_token', $token)
            ->where('is_revoked', false)
            ->first();
    }

    /**
     * Revoke a parent's access token by setting is_revoked to true.
     */
    public function revoke(Guardian $parent): void
    {
        $parent->update(['is_revoked' => true]);
    }
}
