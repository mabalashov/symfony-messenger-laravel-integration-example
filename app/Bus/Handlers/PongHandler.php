<?php

declare(strict_types=1);

namespace App\Bus\Handlers;

use App\Bus\Messages\PingMessage;

class PongHandler
{
    public function handle(PingMessage $message)
    {
        echo PHP_EOL . 'PongHandler:' . PHP_EOL;
        var_dump($message);
    }
}
