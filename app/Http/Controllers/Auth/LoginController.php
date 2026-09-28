<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display the web authentication view.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('seller.orders.index');
        }

        return view('auth.login');
    }

    /**
     * Process standard credentials login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('seller.orders.index'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our registered records.',
        ])->onlyInput('email');
    }

    /**
     * 1-Click Fast Role Switcher for Client Demonstration.
     */
    public function quickLogin(string $email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            return redirect()->route('seller.orders.index')
                ->with('success', "Logged in as {$user->name} ({$user->role})");
        }

        return redirect()->route('login')->withErrors(['email' => 'Demo account not found.']);
    }

    /**
     * End session and logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
