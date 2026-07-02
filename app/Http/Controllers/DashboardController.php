<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Leader;
use App\Models\Post;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home with quick links and summary counts.
     */
    public function index(): View
    {
        $postCount = Post::count();
        $parentCount = Guardian::where('is_revoked', false)->count();
        $leaderCount = Leader::count();

        return view('dashboard.index', compact('postCount', 'parentCount', 'leaderCount'));
    }
}
