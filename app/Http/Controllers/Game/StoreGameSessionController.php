<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreGameSessionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'player_name' => ['required', 'string', 'max:50'],
        ]);

        $gameSession = GameSession::create([
            'player_name' => $request->player_name,
            'inventory' => [],
        ]);

        return response()->json([
            'game_session_id' => $gameSession->id,
        ]);
    }
}
