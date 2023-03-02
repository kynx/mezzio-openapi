<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\ExtractionException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;

use function assert;
use function is_string;

final class DateTimeImmutableHydrator implements HydratorInterface
{
    public static function hydrate(mixed $data): DateTimeImmutable
    {
        assert(is_string($data));
        try {
            return new DateTimeImmutable($data);
        } catch (Exception $exception) {
            throw HydrationException::fromThrowable(DateTimeImmutable::class, $exception);
        }
    }

    public static function extract(mixed $object): string
    {
        if (! $object instanceof DateTimeImmutable) {
            throw ExtractionException::invalidObject($object, DateTimeImmutable::class);
        }
        return $object->format(DateTimeInterface::RFC3339_EXTENDED);
    }
}
