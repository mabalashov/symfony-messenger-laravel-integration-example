<?php

declare(strict_types=1);

namespace App\Bus;

use Illuminate\Support\Collection;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;

final class DispatcherPool
{
    private Collection $pool;

    public function __construct(Collection $pool)
    {
        $this->pool = $pool;
    }

    public function send(string $channel, Envelope $envelope)
    {
        $sender = $this->getSender($channel);

        $sender->send($envelope);
    }

    private function getSender($channel): SenderInterface
    {
        $sender = $this->pool->get($channel);

        if (is_null($sender)) {
            throw new \Exception("Unknown channel: ${channel}");
        }

        if (!($sender instanceof SenderInterface)) {
            throw new \Exception("Channel ${channel} should implements SenderInterface");
        }

        return $sender;
    }
}
