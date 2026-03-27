<?php

use App\Http\Controllers\Game\AskAiQuestionController;
use App\Http\Controllers\Game\IndexGameController;
use App\Http\Controllers\Game\StartGameController;
use App\Http\Controllers\Game\StoreActionController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexGameController::class)->name('home');
Route::redirect('dashboard', '/')->name('dashboard');
Route::post('/game/start', StartGameController::class)->name('game.start');
Route::post('/game/{gameSession}/action', StoreActionController::class)->name('game.action');
Route::post('/game/{gameSession}/ai-assist', AskAiQuestionController::class)->name('game.ai-assist');

require __DIR__.'/settings.php';
