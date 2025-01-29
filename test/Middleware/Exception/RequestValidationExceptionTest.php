<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware\Exception;

use Kynx\Mezzio\OpenApi\Middleware\Exception\RequestValidationException;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidBody;
use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\Schema\Exception\KeywordMismatch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestValidationException::class)]
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

    public function testValidationFailedPreviousSchemaMismatch(): void
    {
        $message  = "Required property 'foo' must be present in the object";
        $expected = "Keyword validation failed: $message";

        $address     = new OperationAddress('/foo', 'PUT');
        $previous    = KeywordMismatch::fromKeyword('foo', [], $message);
        $invalidBody = InvalidBody::becauseBodyDoesNotMatchSchema('application/json', $address, $previous);
        $exception   = RequestValidationException::validationFailed($invalidBody);

        self::assertSame(400, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
        self::assertSame($invalidBody, $exception->getPrevious());
    }

    public function testValidationFailedSetsMessageAndCode(): void
    {
        $previous  = new ValidationFailed('Foo');
        $exception = RequestValidationException::validationFailed($previous);
        self::assertSame(400, $exception->getCode());
        self::assertSame($previous->getMessage(), $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }
}
