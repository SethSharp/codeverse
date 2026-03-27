<?php

namespace App\Http\Controllers\Game;

use App\Ai\Agents\AiAssistant;
use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AskAiQuestionController extends Controller
{
    public function __invoke(Request $request, GameSession $gameSession): JsonResponse
    {
        $request->validate([
            'narrative' => ['required', 'string'],
            'hint' => ['nullable', 'string'],
        ]);

        $shouldFail = random_int(1, 4) === 1;

        $context = "The current code challenge is:\n\n{$request->narrative}";

        if ($request->hint) {
            $context .= "\n\nHint: {$request->hint}";
        }

        $agent = new AiAssistant($shouldFail);
        $response = $agent->prompt($context);

        return response()->json([
            'suggestion' => (string) $response,
        ]);
    }
}
