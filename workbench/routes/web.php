<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::resource('schools', \Workbench\App\Http\Controllers\SchoolController::class);

//Route::post('/schools', [SchoolController::class, 'store']);
