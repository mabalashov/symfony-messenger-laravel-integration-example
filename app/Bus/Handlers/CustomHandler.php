<?php

declare(strict_types=1);

namespace App\Bus\Handlers;

use App\Bus\Messages\CustomMessage;

class CustomHandler
{
    public function handle(CustomMessage $message)
    {
        echo PHP_EOL . 'CustomHandler:' . PHP_EOL;
        var_dump($message);
    }
}
