<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/customer')->as('customer.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'CustomerController@index')->name('index');
    Route::get('/create', 'CustomerController@create')->name('create');
    Route::post('/store', 'CustomerController@store')->name('store');
    Route::get('/edit/{customer}', 'CustomerController@edit')->name('edit');
    Route::post('/update/{customer}', 'CustomerController@update')->name('update');
    Route::get('/destroy/{customer}', 'CustomerController@destroy')->name('destroy');
});
