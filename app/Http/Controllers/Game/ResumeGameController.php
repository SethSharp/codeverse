<?php

namespace App\Http\Controllers\Game;

use App\Ai\Agents\DungeonMaster;
use App\Ai\Concerns\ParsesJsonResponse;
use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;

class ResumeGameController extends Controller
{
    use ParsesJsonResponse;

    public function __invoke(GameSession $gameSession): JsonResponse
    {
        $agent = new DungeonMaster($gameSession);

        if ($gameSession->conversation_id) {
            $response = $agent
                ->continue($gameSession->conversation_id, as: $gameSession)
                ->prompt('The player is resuming their game. Recap where they are in 1-2 sentences, then present the current encounter choices again. Do NOT advance to the next encounter.');
        } else {
            $response = $agent
                ->forUser($gameSession)
                ->prompt('Start the game. My name is '.$gameSession->player_name.'.');

            $gameSession->update([
                'conversation_id' => $response->conversationId,
            ]);
        }

        $data = self::parseResponse((string) $response);

        $gameSession->update([
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
