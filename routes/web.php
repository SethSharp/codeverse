<?php

use App\Http\Controllers\Game\AskAiQuestionController;
use App\Http\Controllers\Game\DeleteGameController;
use App\Http\Controllers\Game\HistoryController;
use App\Http\Controllers\Game\IndexGameController;
use App\Http\Controllers\Game\ResumeGameController;
use App\Http\Controllers\Game\ShowGameController;
use App\Http\Controllers\Game\StoreActionController;
use App\Http\Controllers\Game\StoreGameSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexGameController::class)->name('home');
Route::redirect('dashboard', '/')->name('dashboard');
Route::get('/game/history', HistoryController::class)->name('game.history');
Route::get('/game/{gameSession}', ShowGameController::class)->name('game.show');
Route::post('/game/start', StoreGameSessionController::class)->name('game.start');
Route::post('/game/{gameSession}/resume', ResumeGameController::class)->name('game.resume');
Route::post('/game/{gameSession}/action', StoreActionController::class)->name('game.action');
Route::post('/game/{gameSession}/ai-assist', AskAiQuestionController::class)->name('game.ai-assist');
Route::delete('/game/{gameSession}', DeleteGameController::class)->name('game.delete');

require __DIR__.'/settings.php';
