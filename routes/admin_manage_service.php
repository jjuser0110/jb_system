<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/manage-case')
    ->name('admin.manage-case.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/', [AdminServiceCaseController::class, 'index'])
            ->name('index');

        // UPDATE STATUS (FIXED)
        Route::post('/{serviceCase}/status', [AdminServiceCaseController::class, 'updateStatus'])
            ->name('status');

        // PAYMENT
        Route::post('/{serviceCase}/payment', [AdminServiceCaseController::class, 'updatePayment'])
            ->name('payment');
    });