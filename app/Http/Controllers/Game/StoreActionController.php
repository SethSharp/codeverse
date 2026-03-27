<?php

namespace App\Http\Controllers\Game;

use App\Ai\Agents\DungeonMaster;
use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreActionController extends Controller
{
    public function __invoke(Request $request, GameSession $gameSession): JsonResponse
    {
        $request->validate([
            'choice' => ['nullable', 'string', 'in:A,B,C'],
            'choice_text' => ['nullable', 'string', 'max:500'],
            'answer' => ['nullable', 'string', 'max:500'],
        ]);

        if ($gameSession->game_over) {
            return response()->json(['error' => 'Game is already over.'], 422);
        }

        $prompt = $request->answer
            ? "My answer: {$request->answer}"
            : "I choose: [{$request->choice}] {$request->choice_text}";

        $agent = new DungeonMaster($gameSession);
        $response = $agent
            ->continue($gameSession->conversation_id, as: $gameSession)
            ->prompt($prompt);

        $data = StartGameController::parseResponse((string) $response);

        $gameSession->update([
            'health' => $data['health'],
            'energy' => $data['energy'],
            'inventory' => $data['inventory'],
            'current_encounter' => $data['encounter'],
            'game_over' => $data['game_over'],
            'victory' => $data['victory'],
        ]);

        return response()->json($data);
    }
}
