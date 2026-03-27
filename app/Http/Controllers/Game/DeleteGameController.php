<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\RedirectResponse;

class DeleteGameController extends Controller
{
    public function __invoke(GameSession $gameSession): RedirectResponse
    {
        $gameSession->delete();

        return redirect()->route('home');
    }
}
