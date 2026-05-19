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
    Route::get('/pending', [ServiceCaseController::class, 'pending'])->name('pending');
    Route::get('/accepted', [ServiceCaseController::class, 'accepted'])->name('accepted');
    Route::get('/completed', [ServiceCaseController::class, 'completed'])->name('completed');
    Route::get('/accept/{serviceCase}', [ServiceCaseController::class, 'accept'])->name('accept');
    Route::post('/complete/{serviceCase}', [ServiceCaseController::class, 'complete'])->name('complete');
    Route::get('/toggle-payment/{serviceCase}', [ServiceCaseController::class, 'togglePayment'])->name('toggle-payment');
    Route::get('/export', [ServiceCaseController::class, 'export'])->name('export');
});