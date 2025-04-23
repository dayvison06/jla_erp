<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/admin', function (){
    return Inertia::render('admin/Dashboard');
})->middleware(['auth','admin'])->name('admin.dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
