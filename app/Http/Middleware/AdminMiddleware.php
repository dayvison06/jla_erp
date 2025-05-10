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
        // Verifica se o usuário é admin
        $hasAccessAdmin = session()->has('hasAccessAdmin', true);

        if (!$hasAccessAdmin) {
            return redirect()->route('dashboard')->with('unauthorized', 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
