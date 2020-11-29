<?php

namespace App\Listeners;

use App\Events\BusMessageFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BusMessageFailedListener
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
    public function handle(BusMessageFailed $event)
    {
        echo PHP_EOL . 'BusMessageFailed Event' . PHP_EOL;
        var_dump($event->getMessage());
    }
}
