<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function __invoke(): Response
    {
        $sessions = GameSession::latest()
            ->get()
            ->map(fn (GameSession $session) => [
                'id' => $session->id,
                'player_name' => $session->player_name,
                'health' => $session->health,
                'energy' => $session->energy,
                'current_encounter' => $session->current_encounter,
                'game_over' => $session->game_over,
                'victory' => $session->victory,
                'created_at' => $session->created_at->diffForHumans(),
            ]);

        return Inertia::render('Game/History', [
            'sessions' => $sessions,
        ]);
    }
}
