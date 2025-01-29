<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware\Exception;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidOperationException::class)]
final class InvalidOperationExceptionTest extends TestCase
{
    public function testMissingPointerSetsMessageAndCode(): void
    {
        $expected  = "Request does not contain a pointer for '/paths/foo/get'";
        $exception = InvalidOperationException::missingPointer('/paths/foo/get');
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }

    public function testMissingRequestFactorySetsMessageAndCode(): void
    {
        $expected  = "No request factory configured for '/paths/foo/get'";
        $exception = InvalidOperationException::missingRequestFactory('/paths/foo/get');
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
