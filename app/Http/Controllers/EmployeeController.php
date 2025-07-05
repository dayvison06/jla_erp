<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index() : Response
    {
        $employees = Employee::all()->take(10);
        return Inertia::render('Employees', ['employees' => $employees]);
    }

    public function store (EmployeeRequest $request) : RedirectResponse
    {
        $data = $request->all();
        Employee::create($data['employee']);

        return redirect()->route('employees')->with('notify', [
            'type' => 'success',
            'title' => 'Funcionário Adicionado',
            'message' => 'O funcionário foi adicionado com sucesso.',
        ]);
    }
}
