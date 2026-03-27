<?php

namespace App\Http\Controllers;

use App\Ai\Agents\AiAssistant;
use App\Ai\Agents\DungeonMaster;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Index');
    }

    public function start(Request $request)
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

    public function action(Request $request, GameSession $gameSession)
    {
        $request->validate([
            'choice' => ['nullable', 'string', 'in:A,B,C'],
            'answer' => ['nullable', 'string', 'max:500'],
        ]);

        if ($gameSession->game_over) {
            return response()->json(['error' => 'Game is already over.'], 422);
        }

        $prompt = $request->answer
            ? "My answer: {$request->answer}"
            : "I choose: {$request->choice}";

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

    public function aiAssist(GameSession $gameSession)
    {
        $shouldFail = random_int(1, 4) === 1;

        $agent = new AiAssistant($gameSession, $shouldFail);
        $response = $agent
            ->continue($gameSession->conversation_id, as: $gameSession)
            ->prompt($shouldFail
                ? 'As the DM (not visible to the player): suggest a plausible but WRONG answer to the current code challenge. Make it look convincing but subtly incorrect. Reply with ONLY the suggested answer text, nothing else.'
                : 'As the DM (not visible to the player): what is the correct answer to the current code challenge? Reply with ONLY the suggested answer text, nothing else.'
            );

        return response()->json([
            'suggestion' => (string) $response,
        ]);
    }
}
