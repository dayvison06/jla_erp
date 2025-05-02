<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/teams', [AdminController::class, 'teams'])->name('admin.teams');
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/permissions', [AdminController::class, 'permissions'])->name('admin.permissions');

})->middleware(['auth','admin']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
