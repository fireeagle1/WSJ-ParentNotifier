<?php

namespace App\Services;

use App\Mail\WelcomeParentNotification;
use App\Models\Guardian;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SignUpService
{
    public function __construct(
        protected TokenService $tokenService
    ) {}

    /**
     * Register a new parent/guardian.
     *
     * @param string $email The parent's email address
     * @param string $youngPersonName The name of the young person
     * @return Guardian The newly created Guardian record
     *
     * @throws ValidationException If the email is already registered
     */
    public function register(string $email, string $youngPersonName): Guardian
    {
        // Check for duplicate email
        if (Guardian::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['A parent with this email address is already registered.'],
            ]);
        }

        // Generate unique access token
        $token = $this->tokenService->generate();

        // Create and return the Guardian record
        $guardian = Guardian::create([
            'email' => $email,
            'young_person_name' => $youngPersonName,
            'access_token' => $token,
            'is_revoked' => false,
        ]);

        // Send welcome email with their personal access link
        try {
            Mail::to($guardian->email)->send(new WelcomeParentNotification($guardian));
        } catch (\Throwable $e) {
            Log::warning('Failed to send welcome email to ' . $guardian->email . ': ' . $e->getMessage());
        }

        return $guardian;
    }
}
