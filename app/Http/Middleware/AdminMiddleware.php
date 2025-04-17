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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->permissions()->where('name', 'admin')->exists() || $request->user()->roles()->whereHas('permissions', function ($query) {
                $query->where('name', 'admin');
            })->exists()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->intended()->with('unauthorized', 'Acesso negado!');
        }

        return $next($request);
    }
}
