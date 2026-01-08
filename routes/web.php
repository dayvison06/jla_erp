<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Home');
})->middleware(['auth', 'verified'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('funcionarios')->middleware(['auth', 'verified'])->name('employees.')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    Route::get('/criar', [EmployeeController::class, 'create'])->name('create');
    Route::get('/buscar', [EmployeeController::class, 'search'])->name('search');
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::post('/deactivate', [EmployeeController::class, 'deactivate']);
    Route::patch('/{id}', [EmployeeController::class, 'update']);
    Route::post('/upload/{id}', [EmployeeController::class, 'uploadFiles'])->name('upload');
    Route::post('/importar-excel', [EmployeeController::class, 'importExcel'])->name('import_csv');
    Route::get('/download/template', [EmployeeController::class, 'downloadTemplate']);
    Route::post('ficha-funcionario', [EmployeeController::class, 'generateReport'])->name('employee_report');
});

Route::prefix('administracao')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
        Route::get('/cargos', [EmployeeController::class, 'jobRoles'])->name('job_roles');
        Route::post('/cargos', [EmployeeController::class, 'storeJobRole'])->name('store_job_role');
        Route::get('/cargos/lista', [EmployeeController::class, 'jobRolesList'])->name('job_roles_list');
        Route::get('/departamentos/lista', [EmployeeController::class, 'departmentsList'])->name('departments_list');
        Route::get('/beneficios/lista', [EmployeeController::class, 'benefitsList'])->name('benefits_list');
});

use App\Http\Controllers\WorkController;

Route::prefix('obras')->middleware(['auth', 'verified'])->name('works.')->group(function () {
    Route::get('/', [WorkController::class, 'index'])->name('index');
    Route::get('/lista', [WorkController::class, 'list'])->name('list');
    Route::post('/', [WorkController::class, 'store'])->name('store');
    Route::put('/{id}', [WorkController::class, 'update'])->name('update');
    Route::delete('/{id}', [WorkController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
