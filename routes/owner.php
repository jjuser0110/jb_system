<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/owner')->as('owner.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'OwnerController@index')->name('index');
    Route::get('/create', 'OwnerController@create')->name('create');
    Route::post('/store', 'OwnerController@store')->name('store');
    Route::get('/edit/{owner}', 'OwnerController@edit')->name('edit');
    Route::put('/update/{owner}', [OwnerController::class, 'update'])->name('update');
    Route::get('/destroy/{owner}', 'OwnerController@destroy')->name('destroy');
});
