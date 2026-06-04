<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

// Public routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// Protected routes (require JWT token)
Route::middleware(['auth:api'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Department routes
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentController::class, 'show']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    // Province routes
    Route::get('/provinces', [ProvinceController::class, 'index']);
    Route::post('/provinces', [ProvinceController::class, 'store']);
    Route::get('/provinces/{id}', [ProvinceController::class, 'show']);
    Route::put('/provinces/{id}', [ProvinceController::class, 'update']);
    Route::delete('/provinces/{id}', [ProvinceController::class, 'destroy']);

    // District routes
    Route::get('/districts', [DistrictController::class, 'index']);
    Route::post('/districts', [DistrictController::class, 'store']);
    Route::get('/districts/{id}', [DistrictController::class, 'show']);
    Route::put('/districts/{id}', [DistrictController::class, 'update']);
    Route::delete('/districts/{id}', [DistrictController::class, 'destroy']);

    // Site routes
    Route::get('/sites', [SiteController::class, 'index']);
    Route::post('/sites', [SiteController::class, 'store']);
    Route::get('/sites/{id}', [SiteController::class, 'show']);
    Route::put('/sites/{id}', [SiteController::class, 'update']);
    Route::delete('/sites/{id}', [SiteController::class, 'destroy']);
});
