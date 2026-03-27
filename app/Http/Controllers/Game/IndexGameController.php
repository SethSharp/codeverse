<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexGameController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Game/Index');
    }
}
