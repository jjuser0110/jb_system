<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('service-cases')->as('service-cases.')->group(function () {

    Route::get('/', [ServiceCaseController::class, 'index'])->name('index');
    Route::get('/create', [ServiceCaseController::class, 'create'])->name('create');
    Route::post('/store', [ServiceCaseController::class, 'store'])->name('store');
    Route::get('/edit/{serviceCase}', [ServiceCaseController::class, 'edit'])->name('edit');
    Route::put('/update/{serviceCase}', [ServiceCaseController::class, 'update'])->name('update');
    Route::delete('/destroy/{serviceCase}', [ServiceCaseController::class, 'destroy'])->name('destroy');

});