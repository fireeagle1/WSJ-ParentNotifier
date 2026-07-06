<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Setting;
use App\Services\SignUpService;
use App\Services\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ParentController extends Controller
{
    public function __construct(
        protected SignUpService $signUpService,
        protected TokenService $tokenService,
    ) {}

    /**
     * Display a listing of all parents with email, young person's name,
     * access token link, and revoked status.
     */
    public function index(): View
    {
        $parents = Guardian::orderBy('created_at', 'desc')->get();
        $signupEnabled = Setting::where('key', 'signup_enabled')->first();
        $isSignupEnabled = $signupEnabled && $signupEnabled->value === '1';
        $signupToken = Setting::where('key', 'signup_token')->value('value');

        return view('dashboard.parents.index', compact('parents', 'isSignupEnabled', 'signupToken'));
    }

    /**
     * Manually add a parent (email + young person's name) and generate token via SignUpService.
     */
    public function store(Request $request): RedirectResponse
    {
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

        return redirect()->route('parents.index')
            ->with('success', 'Parent added successfully.');
    }

    /**
     * Revoke parent access via TokenService (sets is_revoked = true).
     */
    public function destroy(Guardian $parent): RedirectResponse
    {
        $this->tokenService->revoke($parent);

        return redirect()->route('parents.index')
            ->with('success', 'Parent access revoked successfully.');
    }

    /**
     * Send a password reset link to a parent/guardian.
     */
    public function sendPasswordReset(Guardian $parent): RedirectResponse
    {
        $status = \Illuminate\Support\Facades\Password::broker('parents')->sendResetLink(
            ['email' => $parent->email]
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return redirect()->route('parents.index')
                ->with('success', "Password reset link sent to {$parent->email}.");
        }

        return redirect()->route('parents.index')
            ->with('error', "Failed to send reset link. Please try again later.");
    }

    /**
     * Toggle the signup_enabled setting value.
     * When enabling, generate a new unique sign-up link token.
     */
    public function toggleSignup(Request $request): RedirectResponse
    {
        $setting = Setting::where('key', 'signup_enabled')->first();
        $currentlyEnabled = $setting && $setting->value === '1';

        if ($currentlyEnabled) {
            // Disable sign-ups
            $setting->update(['value' => '0']);
        } else {
            // Enable sign-ups with a fresh token
            if ($setting) {
                $setting->update(['value' => '1']);
            } else {
                Setting::create(['key' => 'signup_enabled', 'value' => '1']);
            }

            // Generate a new sign-up link token each time it's enabled
            $newToken = \Illuminate\Support\Str::random(32);
            Setting::updateOrCreate(['key' => 'signup_token'], ['value' => $newToken]);
        }

        return redirect()->route('parents.index')
            ->with('success', $currentlyEnabled ? 'Sign-ups disabled.' : 'Sign-ups enabled with a new link.');
    }
}
