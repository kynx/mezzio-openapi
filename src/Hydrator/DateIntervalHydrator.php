<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

use DateInterval;

use Exception;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\ExtractionException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;

use function assert;
use function current;
use function is_string;

/**
 * @see https://datatracker.ietf.org/doc/html/rfc3339#appendix-A
 */
final class DateIntervalHydrator implements HydratorInterface
{
    /** @var array<string, string> */
    private static array $dateMap = [
        'y' => 'Y',
        'm' => 'M',
        'd' => 'D',
    ];

    /** @var array<string, string>  */
    private static array $timeMap = [
        'h' => 'H',
        'i' => 'M',
        's' => 'S',
    ];

    public static function hydrate(mixed $data): DateInterval
    {
        if (is_array($data)) {
            /** @var mixed $data */
            $data = current($data);
        }

        assert(is_string($data));
        // @todo Catch `DateMalformedIntervalStringException` instead once PHP8.2 support dropped
        try {
            return new DateInterval($data);
        } catch (Exception $exception) {
            throw HydrationException::fromThrowable(DateInterval::class, $exception);
        }
    }

    public static function extract(mixed $object): string
    {
        if (! $object instanceof DateInterval) {
            throw ExtractionException::invalidObject($object, DateInterval::class);
        }

        if ($object->invert) {
            throw ExtractionException::unexpectedValue($object, "cannot extract inverted intervals");
        }

        if ($object->f > 0) {
            throw ExtractionException::unexpectedValue($object, "cannot extract intervals with microseconds");
        }

        $duration = 'P';
        foreach (self::$dateMap as $property => $designator) {
            $duration .= self::getDesignatorValue(
                $object,
                $property,
                $designator,
                "cannot extract negative date intervals"
            );
        }
        $time = '';
        foreach (self::$timeMap as $property => $designator) {
            $time .= self::getDesignatorValue(
                $object,
                $property,
                $designator,
                "cannot extract negative time intervals"
            );
        }

        return $duration . ($time === '' ? '' : 'T' . $time);
    }

    private static function getDesignatorValue(
        DateInterval $interval,
        string $property,
        string $designator,
        string $error
    ): string {
        /** @var int $value */
        $value = $interval->$property;
        if ($value < 0) {
            throw ExtractionException::unexpectedValue($interval, $error);
        }
        if ($value === 0) {
            return '';
        }

        return $value . $designator;
    }
}