<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\ExtractionException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;

use function current;
use function is_array;
use function is_string;

/**
 * @see https://datatracker.ietf.org/doc/html/rfc3339#section-5.6
 */
final class DateTimeImmutableHydrator implements HydratorInterface
{
    public static function hydrate(mixed $data): DateTimeImmutable
    {
        if (is_array($data)) {
            /** @var mixed $data */
            $data = current($data);
        }

        if (! is_string($data)) {
            throw HydrationException::fromValue(DateTimeImmutable::class, $data);
        }
        /** @todo Catch DateMalformedStringException once PHP8.2 support is dropped */
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
        return $object->format(DateTimeInterface::RFC3339);
    }
}
