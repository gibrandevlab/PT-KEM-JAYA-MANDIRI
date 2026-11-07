<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/admin');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $loginInput = trim($request->input('username'));
        $password   = $request->input('password');

        // Allow login by username OR email, checking aktif = 1
        $user = User::where(function ($query) use ($loginInput) {
            $query->where('username', $loginInput)
                  ->orWhere('email', $loginInput);
        })
        ->where('aktif', 1)
        ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $request->session()->put('auth.remembered', $request->boolean('remember'));
            $request->session()->put('auth.last_activity', now()->timestamp);

            return redirect()->intended('/admin');
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['login' => 'Username atau password salah!']);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['auth.remembered', 'auth.last_activity']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
