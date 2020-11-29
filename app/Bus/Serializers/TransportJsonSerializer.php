<?php

declare(strict_types=1);

namespace App\Bus\Serializers;

use GBProd\UuidNormalizer\UuidDenormalizer;
use GBProd\UuidNormalizer\UuidNormalizer;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

final class TransportJsonSerializer extends Serializer
{
    public function __construct()
    {
        $serializer = new \Symfony\Component\Serializer\Serializer(
            [
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new DataUriNormalizer(),
                new UuidNormalizer(),
                new UuidDenormalizer(),
                new PropertyNormalizer(),
            ],
            [
                new JsonEncoder(),
            ]
        );

        parent::__construct($serializer);
    }
}
