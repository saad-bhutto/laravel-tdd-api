<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\ModController;
use App\Http\Middleware\ModBelongsToGame;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('games', [GameController::class, 'index']);
Route::get('games/{game}', [GameController::class, 'read']);
Route::post('games', [GameController::class, 'create']);
Route::put('games/{game}', [GameController::class, 'update']);
Route::delete('games/{game}', [GameController::class, 'delete']);

Route::prefix('games/{game}')->middleware([ModBelongsToGame::class])->group(function () {
    Route::get('mods', [ModController::class, 'index']);
    Route::get('mods/{mod}', [ModController::class, 'read']);
    Route::post('mods', [ModController::class, 'create']);
    Route::put('mods/{mod}', [ModController::class, 'update']);
    Route::delete('mods/{mod}', [ModController::class, 'delete']);
});
