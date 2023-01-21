<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Exception;

use Kynx\Mezzio\OpenApi\Operation\Exception\InvalidContentTypeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Operation\Exception\InvalidContentTypeException
 */
final class InvalidContentTypeExceptionTest extends TestCase
{
    public function testFromContentTypeReturnsException(): void
    {
        $contentType = 'image/png';
        $expected    = ['text/csv', 'text/plain'];
        $message     = "Invalid Content-Type header 'image/png'; expected one of 'text/csv, text/plain'";

        $exception = InvalidContentTypeException::fromExpected($contentType, $expected);
        self::assertSame(415, $exception->getCode());
        self::assertSame($contentType, $exception->getContentType());
        self::assertSame($expected, $exception->getExpected());
        self::assertSame($message, $exception->getMessage());
    }
}
