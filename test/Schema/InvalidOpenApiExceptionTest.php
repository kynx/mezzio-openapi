<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Schema;

use Kynx\Mezzio\OpenApi\Schema\InvalidOpenApiException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Schema\InvalidOpenApiException
 */
final class InvalidOpenApiExceptionTest extends TestCase
{
    public function testFromOpenApiErrorsSetsMessageAndCode(): void
    {
        $expected = "Invalid OpenApi document: Bad hair; Bad smell";
        $errors   = ['Bad hair', 'Bad smell'];

        $exception = InvalidOpenApiException::fromOpenApiErrors($errors);
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
