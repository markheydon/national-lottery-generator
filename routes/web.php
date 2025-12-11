<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index']);

Route::get('/game/{game}/generate', [GameController::class, 'generate']);
