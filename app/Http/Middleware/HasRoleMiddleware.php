<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HasRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $currentTeam = $user->currentTeam;
        $userRole = $user?->teams()
                ?->where('team_id', $currentTeam?->id)
                ?->first()?->pivot?->role;

        if ($userRole === null) {
            return response()->json(['message' => 'You do not have a role assigned, please update your profile.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

}
