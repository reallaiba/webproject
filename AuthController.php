<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        if (request('admin')) {
            $this->ensureDefaultAdminExists();
        }

        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($request->boolean('admin_login')) {
            $this->ensureDefaultAdminExists();
            $admin = User::where('email', $credentials['email'])->where('role', 'admin')->first();

            if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
                return back()->withErrors(['email' => 'Invalid admin email or password.'])->onlyInput('email');
            }

            if ($admin->status === 'blocked') {
                return back()->withErrors(['email' => 'This admin account has been blocked.']);
            }

            $request->session()->put('admin_user_id', $admin->id);

            return redirect()->route('admin.dashboard')->with('success', 'Admin panel opened.');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            if (Auth::user()->status === 'blocked') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been blocked.']);
            }

            $request->session()->regenerate();

            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')->with('success', 'Welcome back.')
                : redirect()->route('dashboard')->with('success', 'Welcome back.');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'role' => ['required', Rule::in(['client', 'freelancer'])],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_user_id');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function ensureDefaultAdminExists(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@freelancehub.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
