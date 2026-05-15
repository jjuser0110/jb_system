<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('companies')->as('companies.')->group(function () {

    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/store', [CompanyController::class, 'store'])->name('store');
    Route::get('/edit/{company}', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/update/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/destroy/{company}', [CompanyController::class, 'destroy'])->name('destroy');

});
