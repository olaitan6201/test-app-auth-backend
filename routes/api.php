<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::get('login', function () {
    return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
})->name('login');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', [AuthController::class, 'getUser']);
    
    Route::post('/logout', [AuthController::class, 'logOut']);
});