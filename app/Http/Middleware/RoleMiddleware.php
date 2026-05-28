<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->role;

        // Owner memiliki hak akses penuh ke seluruh fitur sistem
        if ($userRole === 'owner') {
            return $next($request);
        }

        // If no specific roles are required, let them through
        if (empty($roles)) {
            return $next($request);
        }

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        $redirectPath = '/' . $userRole;
        if (in_array($userRole, ['asmen', 'it support'])) {
            $redirectPath = '/admin';
        }

        // Unauthorized: Redirect to their own dashboard
        return redirect($redirectPath)->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}
