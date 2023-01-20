<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Operation\OperationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Operation\OperationException
 */
final class OperationExceptionTest extends TestCase
{
    public function testInvalidContentType(): void
    {
        $expected  = "Invalid Content-Type header 'image/png'; expected one of 'text/csv, text/plain'";
        $exception = OperationException::invalidContentType('image/png', ['text/csv', 'text/plain']);
        self::assertSame(400, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
