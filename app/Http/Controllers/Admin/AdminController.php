<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Mostra a página inicial do admin.
     * @return Response - Retorna a página inicial do admin. (admin/dashboard)
     */
    public function index(Request $request) : Response
    {
        return Inertia::render('admin/Dashboard');
    }

    /**
     * Mostra a página de usuários do admin.
     * @return Response - Retorna a página de usuários do admin. (admin/users)
     */
    public function users(Request $request) : Response
    {
        return Inertia::render('admin/Management/Users');
    }

    /**
     * Mostra a página de equipes do admin.
     * @return Response - Retorna a página de equipes do admin. (admin/teams)
     */
    public function teams(Request $request) : Response
    {
        return Inertia::render('admin/Management/Teams');
    }

    /**
     * Mostra a página de funções do admin.
     * @return Response - Retorna a página de funções do admin. (admin/roles)
     */
    public function roles(Request $request) : Response
    {
        return Inertia::render('admin/Management/Roles');
    }

    /**
     * Mostra a página de permissões do admin.
     * @return Response - Retorna a página de permissões do admin. (admin/permissions)
     */
    public function permissions(Request $request) : Response
    {
        return Inertia::render('admin/Management/Permissions');
    }
}
