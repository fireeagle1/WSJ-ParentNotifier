<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the public landing page.
     */
    public function landing(Request $request)
    {
        // If the user is already authenticated, redirect to dashboard
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        return view('pages.landing');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('pages.privacy');
    }
}
