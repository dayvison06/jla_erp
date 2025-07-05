<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $permissions = $this->loadEveryPermissions($request);

        // Adiciona a variável de sessão hasAcessAdmin  para validação no Middleware
        $request->session()->put('permissions', $permissions);

        if (isset($permissions['administrator'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function loadEveryPermissions(Request $request): array
    {
        $teams = $request->user()->teams()->with('permissions')->get()->pluck('permissions.*.name')->toArray() ?? [];

        $permissions = $request->user()->permissions()->get()->pluck('name')->toArray() ?? [];

        $roles = $request->user()->roles()->with('permissions')->get()->pluck('permissions.*.name')->toArray() ?? [];

        $loadedPermissions = array_merge($permissions, ...$teams, ...$roles);

        return array_unique($loadedPermissions);
    }
}
