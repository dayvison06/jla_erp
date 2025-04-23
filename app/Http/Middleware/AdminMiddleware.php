<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Intercepta a requisição e verifica se o usuário tem permissão de admin.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $teamAdmin = $request->user()->teams()->whereHas('permissions', function ($query) {
            $query->where('name', 'administrator');
        })->exists();

        $permissionAdmin = $request->user()->permissions()->where('name', 'administrator')->exists();

        $roleAdmin = $request->user()->roles()->whereHas('permissions', function ($query) {
            $query->where('name', 'administrator');
        })->exists();

        if (!$teamAdmin || !$permissionAdmin || !$roleAdmin) {
            return redirect()->route('dashboard')->with('unauthorized', 'Você não tem permissão para acessar esta página.');;
        }

        return $next($request);
    }
}
