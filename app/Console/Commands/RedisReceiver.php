<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Messenger\Worker;

class RedisReceiver extends Command
{
    protected $signature = 'bus:receive';

    public function handle(Worker $worker)
    {
        $worker->run();
    }
}
