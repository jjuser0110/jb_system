<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('customer-staff')->as('customer-staff.')->group(function () {

    Route::get('/', [CustomerStaffController::class, 'index'])->name('index');
    Route::get('/create', [CustomerStaffController::class, 'create'])->name('create');
    Route::post('/store', [CustomerStaffController::class, 'store'])->name('store');
    Route::get('/edit/{customerStaff}', [CustomerStaffController::class, 'edit'])->name('edit');
    Route::put('/update/{customerStaff}', [CustomerStaffController::class, 'update'])->name('update');
    Route::delete('/destroy/{customerStaff}', [CustomerStaffController::class, 'destroy'])->name('destroy');

});