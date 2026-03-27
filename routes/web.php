<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
Route::post('/game/{gameSession}/action', [GameController::class, 'action'])->name('game.action');

require __DIR__.'/settings.php';
