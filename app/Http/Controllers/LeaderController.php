<?php

namespace App\Http\Controllers;

use App\Models\Leader;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LeaderController extends Controller
{
    /**
     * Display a listing of all leaders (admin only).
     */
    public function index(): View
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $leaders = Leader::orderBy('created_at', 'desc')->get();

        return view('dashboard.leaders.index', compact('leaders'));
    }

    /**
     * Create a new leader with email, password, and display_name (admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:leaders', 'max:255'],
            'password' => ['required', 'min:8', 'max:128'],
            'display_name' => ['required', 'max:255'],
        ]);

        Leader::create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'display_name' => $validated['display_name'],
            'is_admin' => false,
        ]);

        return redirect()->route('leaders.index')
            ->with('success', 'Leader created successfully.');
    }

    /**
     * Remove a leader (admin only, cannot remove self).
     */
    public function destroy(Leader $leader): RedirectResponse
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if ($leader->id === auth()->id()) {
            return redirect()->route('leaders.index')
                ->with('error', 'You cannot remove yourself.');
        }

        $leader->delete();

        return redirect()->route('leaders.index')
            ->with('success', 'Leader removed successfully.');
    }

    /**
     * Toggle whether a leader is visible to parents on the "Meet the Leaders" page.
     */
    public function toggleVisibility(Leader $leader): RedirectResponse
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $leader->update([
            'visible_to_parents' => !$leader->visible_to_parents,
        ]);

        $status = $leader->visible_to_parents ? 'visible' : 'hidden';

        return redirect()->route('leaders.index')
            ->with('success', "{$leader->display_name} is now {$status} from parents.");
    }

    /**
     * Display the settings page.
     */
    public function settings(): View
    {
        $brandName = Setting::where('key', 'brand_name')->value('value') ?? 'Scouts';
        $brandColor = Setting::where('key', 'brand_color')->value('value') ?? '#7413dc';
        $headerImagePath = Setting::where('key', 'header_image_path')->value('value') ?? '';

        return view('dashboard.settings', compact('brandName', 'brandColor', 'headerImagePath'));
    }

    /**
     * Allow any authenticated leader to update their own profile (display_name, role, bio, photo).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'max:255'],
            'role' => ['nullable', 'max:255'],
            'bio' => ['nullable', 'max:1000'],
            'phone' => ['nullable', 'max:50'],
            'photo' => ['nullable', 'file', 'max:5120', 'mimes:jpeg,png,webp'],
        ]);

        $leader = auth()->user();

        $updateData = [
            'display_name' => $validated['display_name'],
            'role' => $validated['role'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($leader->photo_path && Storage::disk('public')->exists($leader->photo_path)) {
                Storage::disk('public')->delete($leader->photo_path);
            }
            $path = $request->file('photo')->store('leaders', 'public');
            $updateData['photo_path'] = $path;
        }

        $leader->update($updateData);

        return redirect()->back()
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update branding settings (name, color, header image, logo).
     */
    public function updateBranding(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_name' => ['required', 'max:255'],
            'brand_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'header_image' => ['nullable', 'file', 'max:5120', 'mimes:jpeg,png,webp'],
            'logo' => ['nullable', 'file', 'max:2048', 'mimes:jpeg,png,webp,svg'],
        ]);

        Setting::updateOrCreate(['key' => 'brand_name'], ['value' => $validated['brand_name']]);
        Setting::updateOrCreate(['key' => 'brand_color'], ['value' => $validated['brand_color']]);

        if ($request->hasFile('header_image')) {
            // Delete old header image
            $oldPath = Setting::where('key', 'header_image_path')->value('value');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('header_image')->store('branding', 'public');
            Setting::updateOrCreate(['key' => 'header_image_path'], ['value' => $path]);
        }

        if ($request->hasFile('logo')) {
            // Delete old logo
            $oldLogo = Setting::where('key', 'logo_path')->value('value');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('branding', 'public');
            Setting::updateOrCreate(['key' => 'logo_path'], ['value' => $path]);
        }

        return redirect()->back()
            ->with('success', 'Branding updated successfully.');
    }

    /**
     * Remove the header image.
     */
    public function removeHeaderImage(): RedirectResponse
    {
        $oldPath = Setting::where('key', 'header_image_path')->value('value');

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        Setting::updateOrCreate(['key' => 'header_image_path'], ['value' => '']);

        return redirect()->back()
            ->with('success', 'Header image removed.');
    }
}
