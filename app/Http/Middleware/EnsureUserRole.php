<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Forbidden.',
                ], 403);
            }

            if ($user?->isAdmin()) {
                return redirect()->route('admin.monitor.dashboard');
            }

            if ($user?->isMaintenance()) {
                return redirect()->route('sistema');
            }

            if ($user?->isUser()) {
                return redirect()->route('consulta');
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
