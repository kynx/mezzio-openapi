<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ServerExceptionInterface;

use function sprintf;

final class InvalidOperationException extends DomainException implements ServerExceptionInterface
{
    public static function missingPointer(string $path): self
    {
        return new self(sprintf(
            "Request does not contain a pointer for '%s'",
            $path
        ), 500);
    }

    public static function missingOperationFactory(string $pointer): self
    {
        return new self(sprintf(
            "No operation factory configured for '%s'",
            $pointer
        ), 500);
    }
}
