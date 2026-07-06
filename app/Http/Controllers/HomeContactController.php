<?php

namespace App\Http\Controllers;

use App\Models\HomeContact;
use App\Models\Leader;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
     * Store new home contact schedule entries (supports date ranges).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'leader_id' => ['required', 'exists:leaders,id'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'phone' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $from = Carbon::parse($validated['date_from']);
        $to = Carbon::parse($validated['date_to']);
        $period = CarbonPeriod::create($from, $to);

        $created = 0;
        $overwritten = 0;

        foreach ($period as $date) {
            // Remove any existing entry for this date (overwrite)
            $existing = HomeContact::where('date', $date->toDateString())->first();

            if ($existing) {
                $existing->delete();
                $overwritten++;
            }

            HomeContact::create([
                'leader_id' => $validated['leader_id'],
                'date' => $date->toDateString(),
                'phone' => $validated['phone'],
                'notes' => $validated['notes'],
            ]);
            $created++;
        }

        $days = $created === 1 ? '1 day' : "{$created} days";
        $message = "Home contact schedule added for {$days}.";

        if ($overwritten > 0) {
            $message .= " ({$overwritten} existing " . Str::plural('entry', $overwritten) . " replaced.)";
        }

        return redirect()->route('contacts.index')
            ->with('success', $message);
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

    /**
     * Bulk remove home contact entries by leader and date range.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'leader_id' => ['required', 'exists:leaders,id'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        ]);

        $deleted = HomeContact::where('leader_id', $validated['leader_id'])
            ->whereBetween('date', [$validated['date_from'], $validated['date_to']])
            ->delete();

        $days = $deleted === 1 ? '1 entry' : "{$deleted} entries";

        return redirect()->route('contacts.index')
            ->with('success', "Removed {$days} from the schedule.");
    }
}
