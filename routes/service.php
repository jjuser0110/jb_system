<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('services')->name('services.')->group(function () {

    Route::get('/', [ServiceController::class, 'index'])->name('index');

    Route::get('/create', [ServiceController::class, 'create'])->name('create');

    Route::post('/store', [ServiceController::class, 'store'])->name('store');

    Route::get('/edit/{service}', [ServiceController::class, 'edit'])->name('edit');

    Route::put('/update/{service}', [ServiceController::class, 'update'])->name('update');

    Route::get('/destroy/{service}', [ServiceController::class, 'destroy'])->name('destroy');

});