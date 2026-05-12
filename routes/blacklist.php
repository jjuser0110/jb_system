<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function () {
    Route::get('/reasons', [BlacklistController::class, 'index'])->name('reasons.index');
    Route::post('/reasons', [BlacklistController::class, 'store'])->name('reasons.store');
    Route::get('/reasons/{id}/edit', [BlacklistController::class, 'edit'])->name('reasons.edit');
    Route::post('/reasons/{id}/update', [BlacklistController::class, 'update'])->name('reasons.update');
    Route::delete('/reasons/{id}', [BlacklistController::class, 'destroy'])->name('reasons.destroy');
    Route::post('/settings/reasons/ajax-store', [BlacklistController::class, 'ajaxStore'])->name('reasons.ajaxStore');
    Route::get('/blacklist/search', [BlacklistController::class, 'searchForm'])->name('blacklist.search.form');
    Route::post('/blacklist/search', [BlacklistController::class, 'search'])->name('blacklist.search');
});

