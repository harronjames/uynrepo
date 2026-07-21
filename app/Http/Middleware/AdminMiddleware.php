<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $allowedEmail = strtolower((string) config('auth.admin_email'));

        if (
            ! $user
            || ! $user->isAdministrator()
            || strtolower((string) $user->email) !== $allowedEmail
        ) {
            abort(404);
        }

        return $next($request);
    }
}
