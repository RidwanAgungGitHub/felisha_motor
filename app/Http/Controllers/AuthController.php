<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            // Get user role
            $user = Auth::user();

            // Jika user belum punya role, set default sebagai kasir
            if (!isset($user->role) || empty($user->role)) {
                $user->role = 'kasir';
                $user->save();
            }

            // Redirect berdasarkan role dengan URL yang tepat
            if ($user->role == 'admin') {
                return redirect()->to('/admin/dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Admin)');
            } else {
                return redirect()->to('/kasir')->with('success', 'Selamat datang, ' . $user->name . '! (Kasir)');
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
