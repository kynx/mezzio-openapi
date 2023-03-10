<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ServerExceptionInterface;
use Throwable;

use function sprintf;

final class InvalidOperationException extends DomainException implements ServerExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function missingPointer(string $path): self
    {
        return new self(sprintf(
            "Request does not contain a pointer for '%s'",
            $path
        ), 500);
    }

    public static function missingRequestFactory(string $pointer): self
    {
        return new self(sprintf(
            "No request factory configured for '%s'",
            $pointer
        ), 500);
    }
}
