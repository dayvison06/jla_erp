<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee\Employee;
use App\Services\EmployeeServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    protected EmployeeServices $employeeServices;

    public function __construct(EmployeeServices $employeeServices)
    {
        $this->employeeServices = $employeeServices;
    }

    public function index(Request $request) : Response
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            if (is_numeric(preg_replace('/\D/', '', $request->search))) {
                $query->where('cpf', 'like', '%' . preg_replace('/\D/', '', $request->search) . '%');
            } else {
                $query->whereRaw('LOWER(name) LIKE ?', [strtolower($request->search) . '%']);
            }
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('name', 'ASC')->paginate(25);

        return Inertia::render('Employees', [
            'employees' => $employees,
            // outros dados
        ]);

    }

    public function store (EmployeeRequest $request) : RedirectResponse
    {

        $payload = $request->except('attachments');
        Log::info('Recebendo dados para cadastro de funcionário', ['payload' => $payload,]);
        $data = array_filter($payload, fn($value) => !is_array($value));

        try {
            DB::transaction(function () use ($data, $request, &$employee) {
                $employee = Employee::create($data);
                app(EmployeeServices::class)->processDependents($employee, $request->dependents ?? []);
                app(EmployeeServices::class)->processBenefits($employee, $request->benefits ?? []);
            });

            Log::info('Funcionário criado com sucesso: ' . $employee->id);

            if ($request->attachments) {
                foreach ($request->file('attachments') as $file) {
                    $file = $file['file'];
                    $path = $file->store('employees', 'public');
                    $employee->attachments()->create([
                        'employee_id' => $employee->id,
                        'name' => $file->getClientOriginalName(),
                        'type' => $file->getMimeType(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            return redirect()->route('employees.index')->with('notify', [
                'type' => 'success',
                'title' => 'Funcionário Adicionado',
                'message' => 'O funcionário ' . $employee->name . ' foi adicionado com sucesso.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar funcionário: ' . $e->getMessage());
            return redirect()->back()->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível criar o funcionário.',
            ])->withInput();
        }
    }

    public function show ($cpf, Employee $employee) : Response
    {
        $findEmployee = $employee->where('cpf', $cpf)
            ->with('attachments')
            ->first();
        return Inertia::render('Employees', ['employee' => $findEmployee]);
    }

    public function update(FormRequest $request, $cpf): Response
    {
        $payload = $request->all();
        $data = array_filter($payload, fn($value) => !is_array($value));

        Log::info('Recebendo dados para atualização de funcionário', ['data' => $data,]);
        $cpf = $this->employeeServices->cleanCpf($cpf);
        $employee = Employee::where('cpf', $cpf)->firstOrFail();
        try {
            $employee->update($data);
            $this->employeeServices->processDependents($employee, $payload['dependents'] ?? []);
            $employee->benefits()->update($payload['benefits']);
            // Atualizar outros relacionamentos conforme necessário
            return Inertia::render('Employees', ['employee' => $employee])->with('notify', [
                'type' => 'success',
                'title' => 'Funcionário Atualizado',
                'message' => 'Funcionário ' . $employee->name . ' atualizado com sucesso.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar funcionário: ' . $e->getMessage());
            return Inertia::render('Employees', ['employee' => $employee])->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível atualizar o funcionário.',
            ]);
        }
    }

    public function uploadFiles(Request $request, $cpf) : RedirectResponse
    {
        $cpf = $this->employeeServices->cleanCpf($cpf);
        $employee = Employee::where('cpf', $cpf)->firstOrFail();

        foreach ($request->file('attachments') as $file) {
            $file = $file['file'];
            $path = $file->store('employees', 'public');
            $employee->attachments()->create([
                'employee_id' => $employee->id,
                'name' => $file->getClientOriginalName(),
                'type' => $file->getMimeType(),
                'path' => $path,
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Arquivos Enviados',
            'message' => 'Funcionário ' . $employee->name . ' atualizado com sucesso.',
        ]);
    }
}
