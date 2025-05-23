<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user belum punya role, set default sebagai kasir
        if (!isset($user->role) || empty($user->role)) {
            $user->role = 'kasir';
            $user->save();
        }

        // Cek apakah role user sesuai dengan yang dibutuhkan
        if ($user->role !== $role) {
            // Redirect ke halaman yang sesuai dengan role user berdasarkan ROUTES yang ada
            if ($user->role == 'admin') {
                // Admin dashboard menggunakan route '/' dengan name 'dashboard'
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } else {
                // Kasir menggunakan route '/kasir' dengan name 'kasir'
                return redirect()->route('kasir')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
        }

        return $next($request);
    }
}
