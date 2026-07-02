<?php

namespace App\Http\Controllers;

use App\Models\HomeContact;
use App\Models\Leader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeContactController extends Controller
{
    /**
     * Display the home contacts schedule (admin only).
     */
    public function index(): View
    {
        $contacts = HomeContact::with('leader')
            ->orderBy('date', 'asc')
            ->get();

        $leaders = Leader::orderBy('display_name')->get();

        return view('dashboard.contacts.index', compact('contacts', 'leaders'));
    }

    /**
     * Store a new home contact schedule entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'leader_id' => ['required', 'exists:leaders,id'],
            'date' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        HomeContact::create($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Home contact schedule entry added.');
    }

    /**
     * Remove a home contact schedule entry.
     */
    public function destroy(HomeContact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Home contact entry removed.');
    }
}
