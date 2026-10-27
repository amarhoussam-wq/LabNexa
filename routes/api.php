<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/interfacing/mindray/bc3000', [\App\Http\Controllers\Api\InterfacingController::class, 'receiveMindrayResults']);

// ===========================
// Patient Mobile App API
// ===========================
Route::group(['prefix' => 'v1/patient'], function () {
    // Public routes (no auth)
    Route::post('/login', [\App\Http\Controllers\Api\PatientApiController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\PatientApiController::class, 'register']);
    Route::post('/forgot-password', [\App\Http\Controllers\Api\PatientApiController::class, 'forgotPassword']);
    Route::get('/analyses', [\App\Http\Controllers\Api\PatientApiController::class, 'analyses']);
    Route::get('/analyses/{id}', [\App\Http\Controllers\Api\PatientApiController::class, 'analysisDetail']);
    Route::get('/questions/{analysisId}', [\App\Http\Controllers\Api\PatientApiController::class, 'questions']);
    Route::get('/app-version', [\App\Http\Controllers\Api\PatientApiController::class, 'getLatestVersion']);
    Route::get('/file/{path}', [\App\Http\Controllers\Api\PatientApiController::class, 'serveFile'])->where('path', '.*');

    // Protected routes (requires patient token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\PatientApiController::class, 'profile']);
        Route::post('/check-eligibility', [\App\Http\Controllers\Api\PatientApiController::class, 'checkEligibility']);
        Route::get('/reservations', [\App\Http\Controllers\Api\PatientApiController::class, 'reservations']);
        Route::post('/reservations', [\App\Http\Controllers\Api\PatientApiController::class, 'createReservation']);
        Route::get('/results', [\App\Http\Controllers\Api\PatientApiController::class, 'results']);
        Route::get('/notifications', [\App\Http\Controllers\Api\PatientApiController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\PatientApiController::class, 'markNotificationRead']);
        Route::get('/reservations/{id}/pdf', [\App\Http\Controllers\Api\PatientApiController::class, 'downloadPdf']);
        Route::get('/eligibility-report/{id}', [\App\Http\Controllers\Api\PatientApiController::class, 'eligibilityReport']);
        Route::post('/ocr-prescription', [\App\Http\Controllers\Api\PatientApiController::class, 'ocrPrescription']);
        Route::post('/logout', [\App\Http\Controllers\Api\PatientApiController::class, 'logout']);
    });
});

Route::any('{any}', function (Illuminate\Http\Request $request) {

    return response()->json(['message' => 'Path not handled'], 404);
})->where('any', '.*');
