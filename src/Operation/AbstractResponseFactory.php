<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Operation\Exception\InvalidAcceptException;
use Kynx\Mezzio\OpenApi\Serializer\SerializerInterface;
use Laminas\Diactoros\Response;
use Negotiation\BaseAccept;
use Negotiation\Exception\Exception as NegotiationException;
use Negotiation\Negotiator;

use function assert;
use function fopen;
use function fwrite;
use function is_resource;
use function rewind;
use function strtok;

abstract class AbstractResponseFactory
{
    public const int DEFAULT_MAX_MEMORY = 1024 * 1024 * 10;

    public function __construct(protected int $maxMemory = self::DEFAULT_MAX_MEMORY)
    {
    }

    protected function getResponse(string $body, int $status, string $reasonPhrase = '', array $headers = []): Response
    {
        $resource = fopen('php://temp/maxmemory=' . $this->maxMemory, 'r+');
        assert(is_resource($resource));
        fwrite($resource, $body);
        rewind($resource);

        return (new Response($resource, $status, $headers))
            ->withStatus($status, $reasonPhrase);
    }

    protected function getMimeType(
        Negotiator $negotiator,
        SerializerInterface $serializer,
        string $accept,
        array $priorities
    ): string {
        /** @psalm-suppress InvalidCatch Exception interface does not extend Throwable :| */
        try {
            $mimeType = $negotiator->getBest((string) strtok($accept, ';'), $priorities);
        } catch (NegotiationException $exception) {
            throw InvalidAcceptException::fromNegotiationException($exception);
        }

        if (! $mimeType instanceof BaseAccept) {
            throw InvalidAcceptException::unsupportedMimeType($accept);
        }

        if (! $serializer->supports($mimeType->getValue())) {
            throw InvalidAcceptException::unsupportedMimeType($mimeType->getValue());
        }

        return $mimeType->getValue();
    }
}
