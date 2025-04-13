<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobResultController;
use App\Http\Controllers\JobStatusController;
use App\Http\Controllers\JobFinalResultController;
use App\Events\TestBroadcastEvent;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| هذه هي الراوتات الخاصة بـ API، مفعلة باستخدام Sanctum للمصادقة.
*/

// 🔹 Test Pusher
Route::get('/broadcast-test', function () {
    event(new TestBroadcastEvent('Hello from Laravel Broadcast!'));
    return 'Event broadcasted!';
});

Route::get('/test', fn() => view('test'));
Route::view('/monitor', 'job-monitor');

// 🔹 Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🔹 Job Submit (protected)
    Route::middleware('auth:sanctum')->post('/jobs', [JobController::class, 'submit']);
});
Route::get('/jobs', [JobController::class, 'index']);


Route::middleware('auth:sanctum')->get('/auth-check', function (Request $request) {
    return response()->json($request->user());
});


// 🔹 Public Endpoints (results/status - can be moved to protected if needed)
Route::get('/jobs/{jobId}/result', [JobResultController::class, 'show']);
Route::get('/jobs/{jobId}/download', [JobResultController::class, 'download']);
Route::get('/jobs/{job}/status', [JobStatusController::class, 'show']);
