<?php

namespace App\Listeners;

use App\Events\BusMessageHandled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BusMessageHandledListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BusMessageHandled $event)
    {
        echo PHP_EOL . 'BusMessageHandled Event' . PHP_EOL;
        var_dump($event->getReceiverName());
        var_dump($event->getMessage());
    }
}
