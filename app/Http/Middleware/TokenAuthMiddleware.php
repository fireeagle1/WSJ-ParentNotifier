<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    public function __construct(
        protected TokenService $tokenService
    ) {}

    /**
     * Handle an incoming request.
     *
     * Extract the token from the route parameter, validate it via TokenService.
     * If valid, share the Guardian model with the view and proceed.
     * If invalid or revoked, abort with 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        if (!$token) {
            abort(403);
        }

        $guardian = $this->tokenService->validate($token);

        if (!$guardian) {
            abort(403);
        }

        // Share parent data with all views
        View::share('guardian', $guardian);

        // Also set on request for controller access
        $request->attributes->set('guardian', $guardian);

        return $next($request);
    }
}
