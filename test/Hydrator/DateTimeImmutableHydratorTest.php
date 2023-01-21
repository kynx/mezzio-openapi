<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator;

use DateTimeImmutable;
use Kynx\Mezzio\OpenApi\Hydrator\DateTimeImmutableHydrator;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException
 *
 * @covers \Kynx\Mezzio\OpenApi\Hydrator\DateTimeImmutableHydrator
 */
final class DateTimeImmutableHydratorTest extends TestCase
{
    public function testHydrateReturnsHydrated(): void
    {
        $data     = '2023-01-12T16:01:35.000000Z';
        $expected = new DateTimeImmutable($data);
        $actual   = DateTimeImmutableHydrator::hydrate($data);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateInvalidValueThrowsException(): void
    {
        self::expectException(HydrationException::class);
        self::expectExceptionMessage("Cannot hydrate " . DateTimeImmutable::class);
        DateTimeImmutableHydrator::hydrate("bad");
    }
}
