<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/supervisor')->as('supervisor.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'SupervisorController@index')->name('index');
    Route::get('/create', 'SupervisorController@create')->name('create');
    Route::post('/store', 'SupervisorController@store')->name('store');
    Route::get('/edit/{supervisor}', 'SupervisorController@edit')->name('edit');
    Route::post('/update/{supervisor}', 'SupervisorController@update')->name('update');
    Route::get('/destroy/{supervisor}', 'SupervisorController@destroy')->name('destroy');
});
