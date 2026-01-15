<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Imports\EmployeesImport;
use App\Models\Employee\Benefit;
use App\Models\Employee\Departament;
use App\Models\Employee\Employee;
use App\Models\Employee\JobRole;
use App\Services\EmployeeServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    protected EmployeeServices $employeeServices;

    public function __construct(EmployeeServices $employeeServices)
    {
        $this->employeeServices = $employeeServices;
    }

    /**
     * Exibe uma lista paginada de funcionários.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = Employee::query();
        $jobRoles = JobRole::all();
        $departments = Departament::all();
        $per_page = $request->query('per_page', 10);

        // Filtros (job_role, department, status)
        if ($request->filled('status')) {
            $statuses = explode(',', $request->input('status'));
            $query->whereIn('employees.status', $statuses);
        }
        debug('Filtros aplicados', $request->all());
        if ($request->filled('job_role')) {
            $roles = explode(',', $request->input('job_role'));
            $query->whereHas('job_roles', function ($q) use ($roles) {
                $q->whereIn('job_roles.id', $roles);
            });
        }

        if ($request->filled('department')) {
            $depts = explode(',', $request->input('department'));
            $query->whereHas('department', function ($q) use ($depts) {
                $q->whereIn('departments.id', $depts);
            });
        }

        // Search Filter (integrated into main query if passed)
        if ($request->filled('query')) {
            $term = $request->input('query');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('cpf', 'LIKE', '%' . $term . '%')
                    ->orWhere('email', 'LIKE', '%' . $term . '%');
            });
        }

        $employees = $query->with('job_roles', 'department')
            ->select(['id', 'name', 'email', 'civil_state', 'status', 'admission_date', 'photo', 'cpf'])
            ->orderBy('name', 'ASC')
            ->paginate($per_page)
            ->withQueryString();

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
            'filters' => $request->only(['status', 'job_role', 'department', 'query', 'per_page']),
        ]);

    }

    /**
     * Pesquisa funcionários com base em um termo de busca.
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->input('query');

        $employees = Employee::where('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('cpf', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
            ->select(['id', 'name', 'email', 'cpf', 'status'])
            ->orderBy('name', 'ASC')
            ->paginate(10);

        return response()->json($employees);
    }

    /**
     * Exibe o formulário para criar um novo funcionário.
     * @return Response
     */
    public function create()
    {
        return Inertia::render('modules/employees/CreateEmployee');
    }

    /**
     * Armazena um novo funcionário no banco de dados.
     * @param EmployeeRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        $payload = $request->except('attachments');
        Log::info('Recebendo dados para cadastro de funcionário', ['payload' => $payload,]);
        $data = array_filter($payload, fn($value) => !is_array($value));

        DB::beginTransaction();
        try {
            $employee = Employee::create($data);
            app(EmployeeServices::class)->processJobRoles($employee, $request->job_roles ?? []);
            // app(EmployeeServices::class)->processDependents($employee, $request->dependents ?? []);
            app(EmployeeServices::class)->processBenefits($employee, $request->benefits ?? []);

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
            DB::commit();
            return redirect()->route('employees.index')->with('notify', [
                'type' => 'success',
                'title' => 'Funcionário Adicionado',
                'message' => 'O funcionário ' . $employee->name . ' foi adicionado com sucesso.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar funcionário: ' . $e->getMessage());
            return redirect()->back()->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível criar o funcionário.',
            ])->withInput();
        }
    }

    /**
     * Exibe as informações de um funcionário específico.
     * @param int $id
     * @param Employee $employee
     * @return Response
     */
    public function show($id, Employee $employee): Response
    {
        $findEmployee = $employee->where('id', $id)
            ->with('attachments', 'dependents', 'benefits', 'job_roles', 'department')
            ->first();
        return Inertia::render('modules/employees/ShowEmployee', ['employee' => $findEmployee]);
    }

    /**
     * Atualiza as informações de um funcionário específico.
     * @param FormRequest $request
     * @param int $id
     * @return Response
     */
    public function update(FormRequest $request, int $id): Response
    {
        $payload = $request->all();
        $data = array_filter($payload, fn($value) => !is_array($value));

        Log::info('Recebendo dados para atualização de funcionário', ['data' => $data,]);
        debug($data);
        $employee = Employee::where('id', $id)->firstOrFail();
        DB::beginTransaction();
        try {
            $employee->update($data);
            $employee->job_roles()->attach($request->role, ['start_date' => now()]);
            $this->employeeServices->processDependents($employee, $payload['dependents'] ?? []);
            app(EmployeeServices::class)->processBenefits($employee, $request->benefits ?? []);
            // Atualizar outros relacionamentos conforme necessário
            DB::commit();
            return Inertia::render('modules/employees/ShowEmployee', ['employee' => $employee])->with('notify', [
                'type' => 'success',
                'title' => 'Funcionário Atualizado',
                'message' => 'Funcionário ' . $employee->name . ' atualizado com sucesso.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar funcionário: ' . $e->getMessage());
            return Inertia::render('modules/employees/ShowEmployee', ['employee' => $employee])->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível atualizar o funcionário.',
            ]);
        }
    }

    /**
     * Faz o upload de arquivos anexos para um funcionário específico.
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function uploadFiles(Request $request, int $id): RedirectResponse
    {
        $employee = Employee::where('id', $id)->firstOrFail();

        foreach ($request->file('attachments') as $file) {
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
            'message' => 'Os arquivos foram enviados com sucesso.',
        ]);
    }

    /**
     * Remove um anexo específico.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroyAttachment(int $id): RedirectResponse
    {
        try {
            $attachment = \App\Models\Employee\Attachment::findOrFail($id);

            // Remove o arquivo do storage
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }

            $attachment->delete();

            return redirect()->back()->with('notify', [
                'type' => 'success',
                'title' => 'Sucesso',
                'message' => 'Anexo removido com sucesso.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao remover anexo: ' . $e->getMessage());
            return redirect()->back()->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível remover o anexo.'
            ]);
        }
    }

    /**
     * Remove múltiplos anexos.
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyManyAttachments(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attachments,id'
        ]);

        try {
            $attachments = \App\Models\Employee\Attachment::whereIn('id', $validated['ids'])->get();

            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->path)) {
                    Storage::disk('public')->delete($attachment->path);
                }
                $attachment->delete();
            }

            return redirect()->back()->with('notify', [
                'type' => 'success',
                'title' => 'Sucesso',
                'message' => count($attachments) . ' anexos removidos com sucesso.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao remover múltiplos anexos: ' . $e->getMessage());
            return redirect()->back()->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível remover os anexos selecionados.'
            ]);
        }
    }

    /**
     * Exporta múltiplos anexos como ZIP.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
     */
    public function exportAttachments(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attachments,id'
        ]);

        try {
            $attachments = \App\Models\Employee\Attachment::whereIn('id', $validated['ids'])->get();

            if ($attachments->isEmpty()) {
                return redirect()->back()->with('notify', [
                    'type' => 'warning',
                    'title' => 'Avis',
                    'message' => 'Nenhum arquivo encontrado para exportação.'
                ]);
            }

            $zipFileName = 'anexos-exportados-' . date('Y-m-d-H-i-s') . '.zip';
            $zipPath = storage_path('app/public/' . $zipFileName);

            if (!class_exists('ZipArchive')) {
                throw new \Exception('A extensão ZipArchive não está instalada no servidor PHP.');
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($attachments as $attachment) {
                    $filePath = storage_path('app/public/' . $attachment->path);
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, $attachment->name);
                    }
                }
                $zip->close();
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            Log::error('Erro ao exportar anexos: ' . $e->getMessage());
            return redirect()->back()->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível gerar o arquivo ZIP.'
            ]);
        }
    }

    /**
     * Desativa um funcionário com base no ID fornecido.
     * @param Request $request
     * @param Employee $employee
     * @return Response
     */
    public function deactivate(Request $request, Employee $employee): Response
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

    /**
     * Faz o download do template de importação de funcionários em XLSX.
     * @return BinaryFileResponse
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        return response()->download(public_path('assets/docs/templates/template_import_funcionarios.xlsx'), 'template_import_funcionarios.xlsx');
    }

    /**
     * Importa funcionários a partir de um arquivo XLSX.
     * @param Request $request
     * @param Employee $employee
     * @return Response
     */
    public function importExcel(Request $request, Employee $employee): Response
    {
        try {
            $file = $request->file()['importFile'];
            Excel::import(new EmployeesImport(), $file);

            return Inertia::render('modules/employees/Employees')->with('notify', [
                'type' => 'success',
                'title' => 'Importação Concluída',
                'message' => 'Os funcionários foram importados com sucesso.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao importar Excel de funcionários: ' . $e->getMessage());
            return Inertia::render('modules/employees/Employees')->with('notify', [
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Não foi possível importar o arquivo CSV.',
            ]);
        }
    }

    /**
     * Gera um relatório em PDF com as informações dos funcionários selecionados.
     * @param Request $request
     * @return mixed
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
        ]);

        $employeeIds = $validated['employee_ids'];
        $employees = Employee::whereIn('id', $employeeIds)
            ->get();
        debug('Valores encontrados', $employees);

        return Pdf::view('ficha-funcionario', [
            'employees' => $employees,
        ])->download('ficha-funcionarios.pdf');
    }

    /**
     * Lista todos os cargos empresariais.
     * @param Request $request
     * @return Response
     */
    public function jobRoles(Request $request): Response
    {
        $query = JobRole::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $jobRoles = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('modules/administrative/JobRoles', [
            'job_roles' => $jobRoles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Lista todos os departamentos.
     * @param Request $request
     * @return Response
     */
    public function departments(Request $request): Response
    {
        $query = Departament::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $departments = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('modules/administrative/Departments', [
            'departments' => $departments,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Lista todos os benefícios.
     * @param Request $request
     * @return Response
     */
    public function benefits(Request $request): Response
    {
        $query = Benefit::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $benefits = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('modules/administrative/Benefits', [
            'benefits' => $benefits,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Retorna uma lista de todos os cargos empresariais em formato JSON.
     * @return JsonResponse
     */
    public function jobRolesList(): JsonResponse
    {
        $jobRoles = JobRole::all();
        return response()->json($jobRoles);
    }

    /**
     * Retorna uma lista de todos os departamentos em formato JSON.
     * @return JsonResponse
     */
    public function departmentsList(): JsonResponse
    {
        $departments = Departament::all();
        return response()->json($departments);
    }

    /**
     * Retorna uma lista de todos os benefícios em formato JSON.
     * @return JsonResponse
     */
    public function benefitsList(): JsonResponse
    {
        $benefits = Benefit::all();
        return response()->json($benefits);
    }
    /**
     * Castra cargos empresarias com respectivo salário base e descrição.
     * @param Request $request
     * @return JsonResponse
     */
    public function storeJobRole(Request $request)
    {
        $baseSalary = $request->input('base_salary');
        $baseSalary = str_replace(['.', ','], ['', '.'], $baseSalary);

        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'O nome do cargo é obrigatório.',
        ]);

        JobRole::create([
            'name' => $request->input('name'),
            'base_salary' => $baseSalary,
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Cargo criado com sucesso.'
        ]);
    }

    /**
     * Atualiza um cargo.
     */
    public function updateJobRole(Request $request, $id)
    {
        $role = JobRole::findOrFail($id);

        $baseSalary = $request->input('base_salary');
        if ($baseSalary) {
            $baseSalary = str_replace(['.', ','], ['', '.'], $baseSalary);
        }

        $role->update([
            'name' => $request->input('name'),
            'base_salary' => $baseSalary ?? $role->base_salary,
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Cargo atualizado com sucesso.'
        ]);
    }

    /**
     * Remove um cargo.
     */
    public function destroyJobRole($id)
    {
        $role = JobRole::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Cargo removido com sucesso.'
        ]);
    }

    /**
     * Cria um novo departamento.
     */
    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Departament::create($request->only('name', 'description'));

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Departamento criado com sucesso.'
        ]);
    }

    /**
     * Atualiza um departamento.
     */
    public function updateDepartment(Request $request, $id)
    {
        $dept = Departament::findOrFail($id);
        $dept->update($request->only('name', 'description'));

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Departamento atualizado com sucesso.'
        ]);
    }

    /**
     * Remove um departamento.
     */
    public function destroyDepartment($id)
    {
        $dept = Departament::findOrFail($id);
        $dept->delete();

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Departamento removido com sucesso.'
        ]);
    }

    /**
     * Cria um novo benefício.
     */
    public function storeBenefit(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Benefit::create($request->only('name', 'description', 'active'));

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Benefício criado com sucesso.'
        ]);
    }

    /**
     * Atualiza um benefício.
     */
    public function updateBenefit(Request $request, $id)
    {
        $benefit = Benefit::findOrFail($id);
        $benefit->update($request->only('name', 'description', 'active'));

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Benefício atualizado com sucesso.'
        ]);
    }

    /**
     * Remove um benefício.
     */
    public function destroyBenefit($id)
    {
        $benefit = Benefit::findOrFail($id);
        $benefit->delete();

        return redirect()->back()->with('notify', [
            'type' => 'success',
            'title' => 'Sucesso',
            'message' => 'Benefício removido com sucesso.'
        ]);
    }
}
