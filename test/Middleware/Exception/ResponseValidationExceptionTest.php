<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware\Exception;

use Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException;
use League\OpenAPIValidation\PSR7\Exception\NoContentType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException
 */
final class ResponseValidationExceptionTest extends TestCase
{
    public function testValidationFailedSetsMessageAndCode(): void
    {
        $expected      = 'Failed to create a valid response';
        $noContentType = new NoContentType();
        $exception     = ResponseValidationException::validationFailed($noContentType);
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
        self::assertSame($noContentType, $exception->getPrevious());
    }
}
