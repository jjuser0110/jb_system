<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('service')->as('service.')->group(function () {

    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/create', [ServiceController::class, 'create'])->name('create');
    Route::post('/store', [ServiceController::class, 'store'])->name('store');
});
