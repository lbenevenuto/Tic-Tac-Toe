<?php

use App\Http\Controllers\Api\TicTacToeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['StartSession'])->group(function () {
    Route::get('/', [TicTacToeController::class, 'getState']);
    Route::post('/restart', [TicTacToeController::class, 'restartGame']);
    Route::post('/{piece}', [TicTacToeController::class, 'placePiece']);
    Route::delete('/', [TicTacToeController::class, 'resetGame']);
});
