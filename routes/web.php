<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::redirect('dashboard', '/')->name('dashboard');
Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
Route::post('/game/{gameSession}/action', [GameController::class, 'action'])->name('game.action');
Route::post('/game/{gameSession}/ai-assist', [GameController::class, 'aiAssist'])->name('game.ai-assist');

require __DIR__.'/settings.php';
