<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware\Exception;

use Kynx\Mezzio\OpenApi\Middleware\Exception\RequestValidationException;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidParameter;
use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Middleware\Exception\RequestValidationException
 */
final class RequestValidationExceptionTest extends TestCase
{
    public function testValidationFailedRequiredParameterMissing(): void
    {
        $expected  = "Parameter 'foo' is required";
        $previous  = RequiredParameterMissing::fromName('foo');
        $exception = RequestValidationException::validationFailed($previous);
        self::assertSame(400, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testValidationFailedSetsMessageAndCode(): void
    {
        $previous  = InvalidParameter::becauseValueDidNotMatchSchema('foo', 'bar', new SchemaMismatch());
        $exception = RequestValidationException::validationFailed($previous);
        self::assertSame(400, $exception->getCode());
        self::assertSame($previous->getMessage(), $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }
}
