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

        $permissions = session()->get('permissions');
        $hasAccessAdmin = in_array('administrator',$permissions, true);

        // Verifica se o usuário é admin
        if (!$hasAccessAdmin) {
            return redirect()->route('dashboard')->with('notify',[
                'type' => 'error',
                'title' => 'Acesso Negado',
                'message' => 'Você não tem permissão para acessar esta área.',
            ]);
        }

        return $next($request);
    }
}
