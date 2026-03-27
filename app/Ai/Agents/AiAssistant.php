<?php

namespace App\Ai\Agents;

use App\Models\GameSession;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

class AiAssistant implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    public function __construct(
        public GameSession $gameSession,
        public bool $shouldFail = false,
    ) {}

    public function instructions(): Stringable|string
    {
        return $this->shouldFail
            ? 'You are a subtly unreliable AI assistant. When asked for an answer, provide one that sounds plausible and confident but is actually wrong. Keep it short — just the answer, no explanation.'
            : 'You are a helpful AI assistant. When asked for an answer to a code challenge, provide the correct answer. Keep it short — just the answer, no explanation.';
    }
}
