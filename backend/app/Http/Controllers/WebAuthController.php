<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class WebAuthController extends Controller
{
    public function showLoginForm()
    {
        // First-run: if no user exists, redirect to setup admin
        if (User::count() === 0) {
            return redirect()->route('setup');
        }
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // Algorithm-aware check to avoid Bcrypt/Argon mismatch exceptions
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            $hash = (string) $user->getAuthPassword();
            $plain = (string) $credentials['password'];
            $ok = false;
            if (str_starts_with($hash, '$2y$')) { // bcrypt
                $ok = app('hash')->driver('bcrypt')->check($plain, $hash);
            } elseif (str_starts_with($hash, '$argon2')) { // argon2/argon2id
                $ok = app('hash')->driver('argon2id')->check($plain, $hash);
            } else {
                // Fallback to default driver
                $ok = Hash::check($plain, $hash);
            }
            if ($ok) {
                Auth::login($user, $request->boolean('remember'));
                // Create a Sanctum token for API calls from the web panel
                $token = $user->createToken('web')->plainTextToken;
                $request->session()->put('api_token', $token);
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Revoke web token(s) then logout
        if ($request->user()) {
            $request->user()->tokens()->where('name', 'web')->delete();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showSetupForm()
    {
        // If already configured, go to login
        if (User::count() > 0) {
            return redirect()->route('login');
        }
        return view('auth.setup');
    }

    public function setupAdmin(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }
        $data = $request->validate([
            'nom_complet' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','min:6','confirmed'],
            'departement' => ['nullable','string','max:255'],
        ]);

        $admin = User::create([
            'nom_complet' => $data['nom_complet'],
            'email' => $data['email'],
            // Will be hashed via cast or hasher; use Hash::make to be explicit & consistent
            'mot_de_passe' => Hash::make($data['password']),
            'role' => 'admin',
            'departement' => $data['departement'] ?? null,
        ]);

        Auth::login($admin);
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }
}
