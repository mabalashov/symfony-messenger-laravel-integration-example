<?php

namespace App\Console\Commands;

use App\Bus\DispatcherPool;
use App\Bus\Messages\PingMessage;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;

class RedisDispatcher extends Command
{
    protected $signature = 'bus:dispatch';

    public function handle(DispatcherPool $dispatcherPool)
    {
        $message = new PingMessage([
            'id' => Uuid::uuid4(),
        ]);

        $dispatcherPool->send('default', new Envelope($message));
        $dispatcherPool->send('custom', new Envelope($message));

        return 0;
    }
}
