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
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{cpf}', [EmployeeController::class, 'show']);
    Route::put('/', [EmployeeController::class, 'update']);

});

Route::get('/obras', function () {
    return Inertia::render('Works');
})->middleware(['auth', 'verified'])->name('works');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
