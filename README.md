Using symfony messenger in Laravel 
===

Disclaimer
---

This repository only demonstrates integration of `symfony/redis-messenger` to Laravel

Please do not use this code in real projects, because it has a lot of dirty workarounds
 
Please fill free to give me a feedback

How to launch an example
---

I used Laravel's [Homestead](https://laravel.com/docs/8.x/homestead) 
to bootstrap application. So you can run this example by starting Vagrant container and connecting to it:

```bash
vagrant up
vagrant ssh
cd /vagrant
```

There are two commands for demonstrate this example

#### Receiver

```bash
php artisan bus:receive
``` 

Will listen redis queue and dump read messages in console

The example output for test data:
```
PongHandler:
object(App\Bus\Messages\PingMessage)#772 (1) {
  ["payload":"App\Bus\Messages\Message":private]=>
  array(1) {
    ["id"]=>
    string(36) "6a2f15b0-c166-4863-a96e-b662901c9333"
  }
}

BusMessageHandled Event
string(1) "0"
object(App\Bus\Messages\PingMessage)#772 (1) {
  ["payload":"App\Bus\Messages\Message":private]=>
  array(1) {
    ["id"]=>
    string(36) "6a2f15b0-c166-4863-a96e-b662901c9333"
  }
}

PlainHandler:
object(App\Bus\Messages\CustomMessage)#24 (1) {
  ["payload":"App\Bus\Messages\Message":private]=>
  array(1) {
    ["id"]=>
    string(36) "6a2f15b0-c166-4863-a96e-b662901c9333"
  }
}

BusMessageHandled Event
string(1) "1"
object(App\Bus\Messages\CustomMessage)#24 (1) {
  ["payload":"App\Bus\Messages\Message":private]=>
  array(1) {
    ["id"]=>
    string(36) "6a2f15b0-c166-4863-a96e-b662901c9333"
  }
}
```

#### Dispatcher

```bash
php artisan bus:dispatch
```

#### Redis

You can check the messages sent to Redis with the following command:

```bash
redis-cli monitor | grep XADD
```

The example of data gone to redis:
```bash
1606668667.252463 [0 [::1]:33480] "XADD" "queue" "*" "message" "s:162:\"{\"body\":\"{\\\"payload\\\":{\\\"id\\\":\\\"6a2f15b0-c166-4863-a96e-b662901c9333\\\"}}\",\"headers\":{\"type\":\"App\\\\Bus\\\\Messages\\\\PingMessage\",\"Content-Type\":\"application\\/json\"}}\";"
1606668667.253650 [0 [::1]:33482] "XADD" "queue-custom" "*" "message" "s:73:\"{\"body\":\"{\\\"id\\\":\\\"6a2f15b0-c166-4863-a96e-b662901c9333\\\"}\",\"headers\":[]}\";"
``` 

Will send messages in queue.
 

Code overview
---

- `app\Bus\Serializers` - JSON serialization of messages
- `app\Bus\Messages` - Messages to be sent in queue
- `app\Bus\Handlers` - Handlers to process messages received from queue
- `app\Bus\DispatcherPool` - The simple pool which helps send messages in different channels 
- `app\Events` - Events dispatched on success or failed message processing
- `app\Listeners` - Listeners of events
- `app\Providers\BusServiceProvider` - Service Provider which binds all components together


Serialization
---

I have implemented two serializers:
- `TransportJsonSerializer` - default serializer got from symfony package, and tuned a bit
- `TransportJsonCustomSerializer` - Custom serializer which could be deeply configured

If you want to communicate with components created using another frameworks or languages you should continue 
the ideas of `TransportJsonCustomSerializer`

Unfortunately, `symfony/redis-messenger` doesn't allow modify messages in queue on 
[any way you want](https://github.com/symfony/redis-messenger/blob/5.x/Transport/RedisSender.php#L45).
Messages should contain `body` and `headers` fields.

So, if your vendor component send messages with another structure you will have some headache to receive them correctly

