<?php

namespace App\Http\Controllers;

use App\Ai\Agents\DungeonMaster;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Ai\Lab;

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
            'choice' => ['required', 'string', 'in:A,B,C'],
        ]);

        if ($gameSession->game_over) {
            return response()->json(['error' => 'Game is already over.'], 422);
        }

        $agent = new DungeonMaster($gameSession);
        $response = $agent
            ->continue($gameSession->conversation_id, as: $gameSession)
            ->prompt("I choose: {$request->choice}");

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
