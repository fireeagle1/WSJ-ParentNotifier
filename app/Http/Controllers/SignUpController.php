<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SignUpService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SignUpController extends Controller
{
    public function __construct(
        protected SignUpService $signUpService
    ) {}

    /**
     * Show the sign-up form if sign-ups are enabled and token is valid.
     */
    public function show(?string $token = null)
    {
        if (!$this->isSignupAccessible($token)) {
            return view('signup.form', ['closed' => true]);
        }

        return view('signup.form', ['closed' => false, 'signupToken' => $token]);
    }

    /**
     * Handle the sign-up form submission.
     */
    public function store(Request $request, ?string $token = null)
    {
        if (!$this->isSignupAccessible($token)) {
            return view('signup.form', ['closed' => true]);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'young_person_name' => ['required', 'max:255'],
        ]);

        try {
            $this->signUpService->register(
                $validated['email'],
                $validated['young_person_name']
            );
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->back()->with('success', 'Registration successful! Please check your email for your personal access link to view expedition updates.');
    }

    /**
     * Check if sign-ups are enabled and the provided token matches.
     */
    private function isSignupAccessible(?string $token): bool
    {
        $signupEnabled = Setting::where('key', 'signup_enabled')->first();
        if (!$signupEnabled || $signupEnabled->value !== '1') {
            return false;
        }

        $storedToken = Setting::where('key', 'signup_token')->value('value');

        // If there's a stored token, the URL token must match
        if ($storedToken && $token !== $storedToken) {
            return false;
        }

        return true;
    }
}
