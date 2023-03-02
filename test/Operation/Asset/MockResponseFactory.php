<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Asset;

use Kynx\Mezzio\OpenApi\Operation\AbstractResponseFactory;
use Kynx\Mezzio\OpenApi\Serializer\SerializerInterface;
use Laminas\Diactoros\Response;
use Negotiation\Negotiator;

final class MockResponseFactory extends AbstractResponseFactory
{
    public function getResponse(
        string $body,
        int $status,
        string $reasonPhrase = '',
        array $headers = []
    ): Response {
        return parent::getResponse($body, $status, $reasonPhrase, $headers);
    }

    public function getMimeType(
        Negotiator $negotiator,
        SerializerInterface $serializer,
        string $accept,
        array $priorities
    ): string {
        return parent::getMimeType($negotiator, $serializer, $accept, $priorities);
    }
}
