<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Get user
            $user = Auth::user();

            // Pastikan user punya role, jika tidak set ke kasir
            if (empty($user->role)) {
                User::where('id', $user->id)->update(['role' => 'kasir']);
                // Refresh auth user
                Auth::setUser(User::find($user->id));
                $user = Auth::user();
            }

            // Redirect berdasarkan role - LANGSUNG KE URL tanpa middleware dulu
            if ($user->role === 'admin') {
                return redirect('/admin-dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Admin)');
            } else {
                return redirect('/kasir-dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Kasir)');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userName = Auth::user() ? Auth::user()->name : 'User';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sampai jumpa, ' . $userName . '!');
    }
}
