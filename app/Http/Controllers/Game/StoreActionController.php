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

        $action = $request->answer
            ? "My answer: {$request->answer}"
            : "I choose: [{$request->choice}] {$request->choice_text}";

        $inventory = implode(', ', $gameSession->inventory ?? []) ?: 'none';

        $prompt = <<<PROMPT
        CURRENT GAME STATE:
        - Encounter: {$gameSession->current_encounter}/8
        - Health: {$gameSession->health}/100
        - Energy: {$gameSession->energy}/100
        - Inventory: {$inventory}

        PLAYER ACTION: {$action}

        Continue the game from encounter {$gameSession->current_encounter}. Advance to the NEXT encounter ({$gameSession->current_encounter} + 1). Do not repeat the current encounter.
        PROMPT;

        $agent = new DungeonMaster($gameSession);
        $response = $agent
            ->continue($gameSession->conversation_id, as: $gameSession)
            ->prompt($prompt);

        $gameSession->update([
            'health' => $response['health'],
            'energy' => $response['energy'],
            'inventory' => $response['inventory'],
            'current_encounter' => $response['encounter'],
            'game_over' => $response['game_over'],
            'victory' => $response['victory'],
        ]);

        return response()->json([
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
