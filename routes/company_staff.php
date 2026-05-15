<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('company-staff')->as('company-staff.')->group(function () {

    Route::get('/', [CompanyStaffController::class, 'index'])->name('index');
    Route::get('/create', [CompanyStaffController::class, 'create'])->name('create');
    Route::post('/store', [CompanyStaffController::class, 'store'])->name('store');
    Route::get('/edit/{companyStaff}', [CompanyStaffController::class, 'edit'])->name('edit');
    Route::put('/update/{companyStaff}', [CompanyStaffController::class, 'update'])->name('update');
    Route::delete('/destroy/{companyStaff}', [CompanyStaffController::class, 'destroy'])->name('destroy');

});