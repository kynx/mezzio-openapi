<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator;

use DateTimeImmutable;
use Kynx\Mezzio\OpenApi\Hydrator\DateTimeImmutableHydrator;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\ExtractionException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(DateTimeImmutableHydrator::class)]
#[UsesClass(HydrationException::class)]
#[UsesClass(ExtractionException::class)]
final class DateTimeImmutableHydratorTest extends TestCase
{
    public function testHydrateReturnsHydrated(): void
    {
        $data     = '2023-01-12T16:01:35.000000Z';
        $expected = new DateTimeImmutable($data);
        $actual   = DateTimeImmutableHydrator::hydrate($data);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateHydratesArray(): void
    {
        $data     = '2023-01-12T16:01:35.000000Z';
        $expected = new DateTimeImmutable($data);
        $actual   = DateTimeImmutableHydrator::hydrate([$data]);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateNonStringThrowsException(): void
    {
        self::expectException(HydrationException::class);
        self::expectExceptionMessage("Error hydrating " . DateTimeImmutable::class);
        DateTimeImmutableHydrator::hydrate(1.23);
    }

    public function testHydrateInvalidValueThrowsException(): void
    {
        self::expectException(HydrationException::class);
        self::expectExceptionMessage("Cannot hydrate " . DateTimeImmutable::class);
        DateTimeImmutableHydrator::hydrate("bad");
    }

    public function testExtractReturnsDateString(): void
    {
        $expected = '2023-02-23T20:51:37+00:00';
        $dateTime = new DateTimeImmutable($expected);
        $actual   = DateTimeImmutableHydrator::extract($dateTime);
        self::assertSame($expected, $actual);
    }

    public function testExtractInvalidObjectThrowsException(): void
    {
        self::expectException(ExtractionException::class);
        self::expectExceptionMessage("Cannot extract stdClass: expected object of type DateTimeImmutable");
        DateTimeImmutableHydrator::extract(new stdClass());
    }
}
