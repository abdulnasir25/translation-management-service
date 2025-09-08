<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TranslationController;

Route::prefix('v1')->group(function () {
    // Export
    Route::get('/translations/export/{locale}', [TranslationController::class, 'export']);

    Route::get('/translations', [TranslationController::class, 'index']);
    Route::post('/translations', [TranslationController::class, 'store']);
    Route::put('/translations/{id}', [TranslationController::class, 'update']);
    Route::delete('/translations/{id}', [TranslationController::class, 'destroy']);
    Route::get('/translations/{key}/{locale}', [TranslationController::class, 'show']);
});
