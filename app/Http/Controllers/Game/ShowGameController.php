<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Inertia\Inertia;
use Inertia\Response;

class ShowGameController extends Controller
{
    public function __invoke(GameSession $gameSession): Response
    {
        return Inertia::render('Game/Play', [
            'gameSession' => [
                'id' => $gameSession->id,
                'player_name' => $gameSession->player_name,
                'health' => $gameSession->health,
                'energy' => $gameSession->energy,
                'inventory' => $gameSession->inventory,
                'current_encounter' => $gameSession->current_encounter,
                'game_over' => $gameSession->game_over,
                'victory' => $gameSession->victory,
            ],
        ]);
    }
}
