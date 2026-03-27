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

        $gameSession->update([
            'conversation_id' => $response->conversationId,
            'health' => $response['health'],
            'energy' => $response['energy'],
            'inventory' => $response['inventory'],
            'current_encounter' => $response['encounter'],
            'game_over' => $response['game_over'],
            'victory' => $response['victory'],
        ]);

        return response()->json([
            'game_session_id' => $gameSession->id,
            'narrative' => $response['narrative'],
            'input_type' => $response['input_type'],
            'hint' => $response['hint'],
            'choices' => $response['choices'],
            'health' => $response['health'],
            'energy' => $response['energy'],
            'inventory' => $response['inventory'],
            'encounter' => $response['encounter'],
            'encounter_title' => $response['encounter_title'],
            'game_over' => $response['game_over'],
            'victory' => $response['victory'],
        ]);
    }
}
