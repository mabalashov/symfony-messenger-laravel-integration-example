<?php

declare(strict_types=1);

namespace App\Bus\Messages;

class Message
{
    private array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
