<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator;

use DateInterval;
use Kynx\Mezzio\OpenApi\Hydrator\DateIntervalHydrator;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\ExtractionException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateIntervalHydrator::class)]
final class DateIntervalHydratorTest extends TestCase
{
    #[DataProvider('durationProvider')]
    public function testHydrateReturnsValidDateInterval(mixed $duration, DateInterval $expected): void
    {
        $actual = DateIntervalHydrator::hydrate($duration);
        self::assertEquals($expected, $actual);
    }

    public static function durationProvider(): array
    {
        return [
            'string' => ['P1Y', new DateInterval('P1Y')],
            'array'  => [['P1Y', 'invalid'], new DateInterval('P1Y')],
        ];
    }

    public function testHydrateInvalidThrowsException(): void
    {
        self::expectException(HydrationException::class);
        DateIntervalHydrator::hydrate('invalid');
    }

    #[DataProvider('invalidIntervalProvider')]
    public function testExtractInvalidDateIntervalThrowsException(mixed $interval, string $expected): void
    {
        self::expectException(ExtractionException::class);
        self::expectExceptionMessage($expected);
        DateIntervalHydrator::extract($interval);
    }

    public static function invalidIntervalProvider(): array
    {
        $inverted         = new DateInterval('P1Y');
        $inverted->invert = 1; // note - this is supposed to be readonly, but can't figure out another way to set it

        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'not DateInterval' => ['foo', 'expected object of type ' . DateInterval::class],
            'inverted'         => [$inverted, 'cannot extract inverted intervals'],
            'microseconds'     => [DateInterval::createFromDateString('5 microseconds'), 'cannot extract intervals with microseconds'],
            'negative date'    => [DateInterval::createFromDateString('-1 year'), 'cannot extract negative date intervals'],
            'negative time'    => [DateInterval::createFromDateString('-1 hour'), 'cannot extract negative time intervals'],
        ];
        // phpcs:enable
    }

    #[DataProvider('validIntervalProvider')]
    public function testExtractReturnsDuration(string $interval): void
    {
        $dateInterval = new DateInterval($interval);
        $actual       = DateIntervalHydrator::extract($dateInterval);
        self::assertEquals($interval, $actual);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validIntervalProvider(): array
    {
        return [
            'year'    => ['P1Y'],
            'month'   => ['P2M'],
            'day'     => ['P3D'],
            'hour'    => ['PT4H'],
            'minute'  => ['PT5M'],
            'second'  => ['PT6S'],
            'complex' => ['P1Y2M3DT4H5M6S'],
        ];
    }

    public function testExtractHandlesWeeks(): void
    {
        $expected = 'P14D';
        $interval = new DateInterval('P2W');
        $actual   = DateIntervalHydrator::extract($interval);
        self::assertSame($expected, $actual);
    }
}
