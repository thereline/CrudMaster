<?php

use Illuminate\Support\Facades\Route;

Route::resource('v1/schools', \Workbench\App\Http\Controllers\SchoolController::class);
