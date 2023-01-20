<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\ExceptionInterface;
use RuntimeException;

use function implode;
use function sprintf;

final class OperationException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param list<string> $expected
     */
    public static function invalidContentType(string $contentType, array $expected): self
    {
        return new self(sprintf(
            "Invalid Content-Type header '%s'; expected one of '%s'",
            $contentType,
            implode(", ", $expected)
        ), 400);
    }
}
