<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/funcionarios', function () {
    return Inertia::render('Employees');
})->middleware(['auth', 'verified'])->name('employees');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
