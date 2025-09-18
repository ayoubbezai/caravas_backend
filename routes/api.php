<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\GrayCardController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DriverLessonsController;

// post is for the company admin

Route::apiResource('companies', CompaniesController::class);
Route::get('companies/{company}/drivers', [DriverController::class, 'getByCompany']);
Route::apiResource('drivers', DriverController::class);
Route::prefix('drivers')->group(function () {
    // Get all lessons for a specific driver
    Route::get('{driverId}/lessons', [DriverLessonsController::class, 'index']);

    // Create a new lesson for a driver
    Route::post('{driverId}/lessons', [DriverLessonsController::class, 'store']);

    // Get a specific lesson for a driver
    Route::get('{driverId}/lessons/{lessonId}', [DriverLessonsController::class, 'show']);

    // Update a specific lesson for a driver
    Route::put('{driverId}/lessons/{lessonId}', [DriverLessonsController::class, 'update']);

    // Delete a specific lesson for a driver
    Route::delete('{driverId}/lessons/{lessonId}', [DriverLessonsController::class, 'destroy']);
});


Route::get('lessons', [DriverLessonsController::class, 'getAllLessons']);

// Gray card routes
Route::prefix('drivers')->group(function () {
    // Get gray card for a driver
    Route::get('{driverId}/gray-card', [GrayCardController::class, 'show']);

    // Create or update gray card for a driver
    Route::post('{driverId}/gray-card', [GrayCardController::class, 'storeOrUpdate']);

    // Delete gray card for a driver
    Route::delete('{driverId}/gray-card', [GrayCardController::class, 'destroy']);
});

Route::get('gray-cards', [GrayCardController::class, 'index']);


use App\Http\Controllers\InsurancesController;

Route::prefix('insurances')->group(function () {
    Route::get('/', [InsurancesController::class, 'index']);           // List all insurances
    Route::get('{insurance}', [InsurancesController::class, 'show']);  // Show a specific insurance
    Route::post('/', [InsurancesController::class, 'store']);          // Create a new insurance
    Route::put('{insurance}', [InsurancesController::class, 'update']); // Update an existing insurance
    Route::delete('{insurance}', [InsurancesController::class, 'destroy']); // Delete an insurance
});


Route::post('login', [AuthController::class, 'login']);
