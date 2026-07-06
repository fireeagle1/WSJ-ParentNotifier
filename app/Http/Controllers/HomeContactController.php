<?php

namespace App\Http\Controllers;

use App\Models\HomeContact;
use App\Models\Leader;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        foreach ($period as $date) {
            // Skip if an entry already exists for this leader on this date
            $exists = HomeContact::where('leader_id', $validated['leader_id'])
                ->where('date', $date->toDateString())
                ->exists();

            if (!$exists) {
                HomeContact::create([
                    'leader_id' => $validated['leader_id'],
                    'date' => $date->toDateString(),
                    'phone' => $validated['phone'],
                    'notes' => $validated['notes'],
                ]);
                $created++;
            }
        }

        $days = $created === 1 ? '1 day' : "{$created} days";

        return redirect()->route('contacts.index')
            ->with('success', "Home contact schedule added for {$days}.");
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
