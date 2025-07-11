<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function show ($cpf, Employee $employee) : Response
    {
        $findEmployee = $employee->where('cpf', $cpf)->first();

        return Inertia::render('Employees', ['employee' => $findEmployee]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->all();
        try {
            $employee = Employee::where('cpf', $data['employee']['cpf'])->firstOrFail();
            $employee->update($data['employee']);
            Log::info('Funcionário atualizado com sucesso: ' . $employee->id);

            return redirect()->route('employees.index')->with('notify', [
                'type' => 'success',
                'title' => 'Funcionário Atualizado',
                'message' => 'O funcionário foi atualizado com sucesso.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar funcionário: ' . $e->getMessage());
            return redirect()->back()->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível atualizar o funcionário.',
            ]);
        }
    }
}
