<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Home');
})->middleware(['auth', 'verified'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/funcionarios', function () {
    return Inertia::render('Employees');
})->middleware(['auth', 'verified'])->name('employees');

Route::get('/obras', function () {
    return Inertia::render('Works');
})->middleware(['auth', 'verified'])->name('works');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
