<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ServerExceptionInterface;
use Throwable;

use function get_debug_type;
use function sprintf;

final class ExtractionException extends DomainException implements ServerExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidObject(mixed $object, string $expected): self
    {
        return self::unexpectedValue($object, sprintf(
            "expected object of type %s",
            $expected
        ));
    }

    public static function unexpectedValue(mixed $object, string $message): self
    {
        return new self(sprintf(
            "Cannot extract %s: %s",
            get_debug_type($object),
            $message
        ), 500);
    }
}
