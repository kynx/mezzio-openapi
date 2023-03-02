<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\ServerExceptionInterface;
use RuntimeException;
use Throwable;

final class SerializerException extends RuntimeException implements ServerExceptionInterface
{
    public static function unsupportedMimeType(string $mimeType): self
    {
        return new self("Unsupported mime type '$mimeType'", 500);
    }

    public static function fromThrowable(Throwable $exception): self
    {
        return new self("Serialization error: " . $exception->getMessage(), 500, $exception);
    }
}
