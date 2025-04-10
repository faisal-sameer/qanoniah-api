<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobResultController;
use App\Http\Controllers\JobStatusController;
use App\Events\TestBroadcastEvent;
use App\Http\Controllers\JobFinalResultController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
///test Pusher
Route::get('/broadcast-test', function () {
    event(new TestBroadcastEvent('Hello from Laravel Broadcast!'));
    return 'Event broadcasted!';
});

Route::get('/test', fn() => view('test'));

Route::view('/monitor', 'job-monitor');



/////////
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jobs', [JobController::class, 'submit']);
});

//Route::middleware('auth:sanctum')->get('/jobs/{jobId}/result', [JobResultController::class, 'show']);
Route::get('/jobs/{job}/result', [JobResultController::class, 'show']);



//Route::middleware('auth:sanctum')->get('/jobs/{jobId}/status', [JobStatusController::class, 'show']);
Route::get('/jobs/{job}/status', [JobStatusController::class, 'show']);


Route::get('/jobs/{jobId}/final-db', [JobFinalResultController::class, 'show']);
