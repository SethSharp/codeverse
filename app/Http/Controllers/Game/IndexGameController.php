<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Inertia\Inertia;
use Inertia\Response;

class IndexGameController extends Controller
{
    public function __invoke(): Response
    {
        $latestSession = GameSession::where('game_over', false)
            ->latest()
            ->first();

        return Inertia::render('Game/Index', [
            'latestSession' => $latestSession ? [
                'id' => $latestSession->id,
                'player_name' => $latestSession->player_name,
                'health' => $latestSession->health,
                'energy' => $latestSession->energy,
                'current_encounter' => $latestSession->current_encounter,
                'game_over' => $latestSession->game_over,
            ] : null,
        ]);
    }
}
