<?php

namespace App\Http\Controllers\Game;

use App\Ai\Agents\DungeonMaster;
use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StartGameController extends Controller
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

        $agent = new DungeonMaster($gameSession);
        $response = $agent
            ->forUser($gameSession)
            ->prompt('Start the game. My name is '.$gameSession->player_name.'.');

        $data = DungeonMaster::parseResponse((string) $response);

        $gameSession->update([
            'conversation_id' => $response->conversationId,
            'health' => $data['health'],
            'energy' => $data['energy'],
            'inventory' => $data['inventory'],
            'current_encounter' => $data['encounter'],
            'game_over' => $data['game_over'],
            'victory' => $data['victory'],
        ]);

        return response()->json([
            'game_session_id' => $gameSession->id,
            ...$data,
        ]);
    }
}
