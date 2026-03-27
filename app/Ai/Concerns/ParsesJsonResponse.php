<?php

namespace App\Ai\Concerns;

trait ParsesJsonResponse
{
    public static function parseResponse(string $raw): array
    {
        $cleaned = trim($raw);
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```$/', '', $cleaned);

        $data = json_decode($cleaned, true);

        if (! is_array($data)) {
            throw new \RuntimeException('Failed to parse AI response: '.$raw);
        }

        return $data;
    }
}
