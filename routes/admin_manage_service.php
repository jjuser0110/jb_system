<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/manage-case')->name('admin.manage-case.')->middleware('auth')->group(function () {

        Route::get('/', [AdminServiceCaseController::class, 'index'])
            ->name('index');
        Route::post('/{id}/status', [AdminServiceCaseController::class, 'updateStatus'])
            ->name('status');
        Route::post('/{id}/payment', [AdminServiceCaseController::class, 'updatePayment'])
            ->name('payment');
    });