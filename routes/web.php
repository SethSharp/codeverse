<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/game', [GameController::class, 'index'])->name('game.index');
Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
Route::post('/game/{gameSession}/action', [GameController::class, 'action'])->name('game.action');

require __DIR__.'/settings.php';
