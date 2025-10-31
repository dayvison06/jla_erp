<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Imports\EmployeesImport;
use App\Models\Employee\Departament;
use App\Models\Employee\Employee;
use App\Models\Employee\JobRole;
use App\Services\EmployeeServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

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
        $jobRoles = JobRole::all();
        $departments = Departament::all();

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

        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department', $request->department);
        }

        if($request->filled('per_page') && is_numeric($request->per_page)) {
            $perPage = (int) $request->per_page;
        } else {
            $perPage = 10; // valor padrão
        }

      $employees = $query->with('job_roles', 'department')
          ->select(['id', 'name', 'email', 'cpf', 'civil_state', 'status', 'admission_date'])
          ->orderBy('name', 'ASC')
          ->paginate($perPage);

        return Inertia::render('modules/employees/Employees', [
            'employees' => $employees,
            'job_roles' => $jobRoles,
            'departments' => $departments,
            'status' => [
                'active' => 'Ativo',
                'inactive' => 'Inativo',
                'vacation' => 'Em Férias',
                'on_leave' => 'Licença Médica',
                'terminated' => 'Desligado',
            ],
            // outros dados
        ]);

    }

    public function create()
    {
        return Inertia::render('modules/employees/CreateEmployee');
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

    public function show ($id, Employee $employee) : Response
    {
        $findEmployee = $employee->where('id', $id)
            ->with('attachments')
            ->first();
        return Inertia::render('modules/employees/ShowEmployee', ['employee' => $findEmployee]);
    }

    public function update(FormRequest $request, int $id): Response
    {
        $payload = $request->all();
        $data = array_filter($payload, fn($value) => !is_array($value));

        Log::info('Recebendo dados para atualização de funcionário', ['data' => $data,]);

        $employee = Employee::where('id', $id)->firstOrFail();
        try {
            $employee->update($data);
            $this->employeeServices->processDependents($employee, $payload['dependents'] ?? []);
            $employee->benefits()->update($payload['benefits']);
            // Atualizar outros relacionamentos conforme necessário
            return Inertia::render('modules/employees/ShowEmployee', ['employee' => $employee])->with('notify', [
                'type' => 'success',
                'title' => 'Funcionário Atualizado',
                'message' => 'Funcionário ' . $employee->name . ' atualizado com sucesso.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar funcionário: ' . $e->getMessage());
            return Inertia::render('modules/employees/ShowEmployee', ['employee' => $employee])->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível atualizar o funcionário.',
            ]);
        }
    }

    public function uploadFiles(Request $request, int $id) : RedirectResponse
    {
        $employee = Employee::where('id', $id)->firstOrFail();

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

    public function deactivate(Request $request, Employee $employee) : Response
    {
        try {
            $findEmployee = $employee->where('cpf', $request['cpf'])
                ->first();
            $findEmployee->status = 'inactive';
            $findEmployee->save();

            return Inertia::render('Employees', ['employee' => $findEmployee]);
        } catch (\Throwable $e) {
            Log::error('Erro ao desativar funcionário: ' . $e->getMessage());
        }

        return Inertia::render('Employees')->with('notify', [
            'type' => 'error',
            'title' => 'Erro',
            'message' => 'Não foi possível desativar o funcionário.',
        ]);
    }

    public function downloadTemplate()
    {
        return response()->download(storage_path('app/templates/template_import_funcionarios.xlsx'), 'template_import_funcionarios.xlsx');
        // ou: return Storage::download('templates/template.csv');
    }

    public function importCSV(Request $request, Employee $employee) : Response
    {
        $file = $request->file()['importFile'];
        Excel::import(new EmployeesImport(), $file);

        try {
            $file = $request->file()['importFile'];
            $path = $file->getRealPath();

            // Usando PhpSpreadsheet para ler arquivos XLSX/XLS
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $rows = $spreadsheet->getActiveSheet()->toArray();

// Cabeçalho (primeira linha) e resto dos dados
            $header = array_shift($rows);
            $data = $rows;

            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data);



            foreach ($data as $row) {
                $rowData = array_combine($header, $row);
                dd($rowData);
                // Processar cada linha e criar/atualizar funcionários
//                Employee::updateOrCreate(
//                    ['cpf' => $this->employeeServices->cleanCpf($rowData['cpf'])],
//                    [
//                        'name' => $rowData['name'],
//                        'email' => $rowData['email'],
//                        'department' => $rowData['department'],
//                        'job_role' => $rowData['job_role'],
//                        'status' => $rowData['status'],
//                        // Outros campos conforme necessário
//                    ]
//                );
            }

            return Inertia::render('modules/employees/Employees')->with('notify', [
                'type' => 'success',
                'title' => 'Importação Concluída',
                'message' => 'Os funcionários foram importados com sucesso.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao importar CSV de funcionários: ' . $e->getMessage());
            return Inertia::render('modules/employees/Employees')->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível importar o arquivo CSV.',
            ]);
        }
    }
}
