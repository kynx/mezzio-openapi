<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Exception;

use Exception;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException
 */
final class HydrationExceptionTest extends TestCase
{
    public function testFromThrowableReturnsException(): void
    {
        $target   = self::class;
        $previous = new Exception("Foo error", 999);
        $expected = "Cannot hydrate $target: " . $previous->getMessage();

        $exception = HydrationException::fromThrowable(self::class, $previous);
        self::assertSame($target, $exception->getTarget());
        self::assertSame(400, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame($expected, $exception->getMessage());
    }
}
