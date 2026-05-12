<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('customers')->as('customers.')->group(function () {

    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/store', [CustomerController::class, 'store'])->name('store');
    Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/destroy/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

});
