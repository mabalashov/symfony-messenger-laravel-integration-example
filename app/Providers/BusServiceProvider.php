<?php

declare(strict_types=1);

namespace App\Providers;

use App\Bus\DispatcherPool;
use App\Bus\Handlers\CustomHandler;
use App\Bus\Handlers\PongHandler;
use App\Bus\Messages\PingMessage;
use App\Bus\Messages\CustomMessage;
use App\Bus\Serializers\TransportJsonCustomSerializer;
use App\Bus\Serializers\TransportJsonSerializer;
use App\Events\BusMessageFailed;
use App\Events\BusMessageHandled;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\Bridge\Redis\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisReceiver;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisSender;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Worker;

class BusServiceProvider extends ServiceProvider
{
    private array $handlers = [
        PingMessage::class => [
            PongHandler::class,
        ],

        CustomMessage::class => [
            CustomHandler::class,
        ],
    ];

    // should be in config in real application
    private array $queues = [
        'default' => 'localhost:6379/queue',
        'custom' => 'localhost:6379/queue-custom',
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Open/close principle breaker, but it is just an example
        $serializer = new TransportJsonSerializer();
        $plainSerializer = new TransportJsonCustomSerializer();

        $redisReceiver =  new RedisReceiver(Connection::fromDsn($this->queues['default']), $serializer);
        $redisPlainReceiver =  new RedisReceiver(Connection::fromDsn($this->queues['custom']), $plainSerializer);

        $this->app->bind(DispatcherPool::class, function() use ($serializer, $plainSerializer) {
            $senders = collect([
                'default' => new RedisSender(Connection::fromDsn($this->queues['default']), $serializer),
                'custom' => new RedisSender(Connection::fromDsn($this->queues['custom']), $plainSerializer),
            ]);

            return new DispatcherPool($senders);
        });

        $this->app->bind(Worker::class, function(Application $application) use ($redisReceiver, $redisPlainReceiver) {
            $receivers = [
                $redisReceiver,
                $redisPlainReceiver,
            ];

            $handlers = $this->getHandlers();
            $middleware = [
                new HandleMessageMiddleware(
                    new HandlersLocator($handlers), false
                )
            ];
            $messageBus = new MessageBus($middleware);

            $dispatcher = $this->getEventDispatcher();

            return new Worker($receivers, $messageBus, $dispatcher);
        });
    }

    private function getHandlers(): array
    {
        return array_map(function(array $handlerClasses) {
            return array_map(function (string $handlerClass) {
                return new HandlerDescriptor(function($message) use ($handlerClass) {
                    // the instance of handler class will be created each time you receive a message
                    // cache instances if you need
                    return app()->make($handlerClass)->handle($message);
                });
            }, $handlerClasses);
        }, $this->handlers);
    }


    private function getEventDispatcher()
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener(
            WorkerMessageFailedEvent::class,
            function (WorkerMessageFailedEvent $event) {
                $message = $event->getThrowable()->getMessage();

                event(new BusMessageFailed($message));
            }
        );

        $dispatcher->addListener(
            WorkerMessageHandledEvent::class,
            function (WorkerMessageHandledEvent $event) {
                $receiverName = $event->getReceiverName();
                $message = $event->getEnvelope()->getMessage();

                event(new BusMessageHandled($receiverName, $message));
            }
        );

        return $dispatcher;
    }
}
