<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation\Exception;

use DomainException;
use Exception;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;
use Negotiation\Exception\Exception as NegotiationException;

use function assert;

final class InvalidAcceptException extends DomainException implements ClientExceptionInterface
{
    /**
     * @psalm-suppress UndefinedClass Don't know why it thinks NegotiationException does not exist
     * @psalm-suppress TypeDoesNotContainType Upstream needs to extend Throwable :|
     */
    public static function fromNegotiationException(NegotiationException $exception): self
    {
        assert($exception instanceof Exception);
        return new self("Error negotiating Accept header: " . $exception->getMessage(), 415, $exception);
    }

    public static function unsupportedMimeType(string $mimeType): self
    {
        return new self("Unsupported mime type '$mimeType'", 415);
    }
}
