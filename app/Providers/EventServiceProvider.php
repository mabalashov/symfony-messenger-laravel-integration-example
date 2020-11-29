<?php

namespace App\Providers;

use App\Events\BusMessageFailed;
use App\Events\BusMessageHandled;
use App\Listeners\BusMessageFailedListener;
use App\Listeners\BusMessageHandledListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        BusMessageHandled::class => [
            BusMessageHandledListener::class,
        ],

        BusMessageFailed::class => [
            BusMessageFailedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
