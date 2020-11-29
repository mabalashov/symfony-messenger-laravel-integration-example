<?php

declare(strict_types=1);

namespace App\Bus\Serializers;

use App\Bus\Messages\CustomMessage;
use GuzzleHttp\Utils;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * I have skipped support of headers for simplifying this example
 *
 * Class TransportJsonPlainSerializer
 * @package App\Bus\Serializers
 */
class TransportJsonCustomSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];

        $message = new CustomMessage(Utils::jsonDecode($body, true));

        return new Envelope($message, []);
    }

    public function encode(Envelope $envelope): array
    {
        /** @var CustomMessage $message */
        $message = $envelope->getMessage();

        return [
            'body' => Utils::jsonEncode($message->getPayload()),
            'headers' => [],
        ];
    }
}
