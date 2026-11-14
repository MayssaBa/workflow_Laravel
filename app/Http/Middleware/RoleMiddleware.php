<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {

        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->isActive()) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Your account is disabled.'
                ]);
        }

        if (!in_array($request->user()->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
