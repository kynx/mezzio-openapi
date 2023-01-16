<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

use Kynx\Mezzio\OpenApi\ExceptionInterface;
use RuntimeException;
use Throwable;

use function sprintf;

final class HydratorException extends RuntimeException implements ExceptionInterface
{
    public static function missingDiscriminatorProperty(string $parent, string $property): self
    {
        return new self(sprintf("Property '%s' is missing discriminator property '%s'", $parent, $property), 400);
    }

    public static function invalidDiscriminatorValue(string $property, string $value): self
    {
        return new self(sprintf("Discriminator property '%s' has invalid value '%s'", $property, $value), 400);
    }

    public static function fromThrowable(string $classOrProperty, Throwable $throwable): self
    {
        return new self(sprintf("Cannot hydrate %s: %s", $classOrProperty, $throwable->getMessage()), 400, $throwable);
    }
}
