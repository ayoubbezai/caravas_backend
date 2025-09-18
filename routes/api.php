<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CompaniesController;

// post is for the company admin

Route::apiResource('companies', CompaniesController::class);
Route::get('companies/{company}/drivers', [DriverController::class, 'getByCompany']);
Route::apiResource('drivers', DriverController::class);
