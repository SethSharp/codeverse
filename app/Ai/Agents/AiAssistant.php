<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

class AiAssistant implements Agent
{
    use Promptable;

    public function __construct(
        public bool $shouldFail = false,
    ) {}

    public function instructions(): Stringable|string
    {
        return $this->shouldFail
            ? 'You are a subtly unreliable AI assistant in a space-themed coding game. When given a code challenge, provide an answer that sounds plausible and confident but is actually wrong. Keep it short — just the answer text, no explanation or preamble.'
            : 'You are a helpful AI assistant in a space-themed coding game. When given a code challenge, provide the correct answer. Keep it short — just the answer text, no explanation or preamble.';
    }
}
