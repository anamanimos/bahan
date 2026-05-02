<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // If user has no roles, they can only access dashboard, claim route, and logout
        if ($user->roles->count() === 0) {
            if ($request->is('dashboard') || $request->is('claim-admin') || $request->is('logout')) {
                return $next($request);
            }
            return redirect('/dashboard')->with('error', 'Akun Anda belum memiliki hak akses.');
        }

        // If specific roles are passed to the middleware
        if (!empty($roles)) {
            // Admin can bypass everything if they are one of the allowed roles
            // Or if no roles are passed, but here we require them.
            if (!$user->hasAnyRole($roles)) {
                return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
            }
        }

        return $next($request);
    }
}
