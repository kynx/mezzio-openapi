<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\Exception\RequestValidationException;
use Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException;
use Kynx\Mezzio\OpenApi\Middleware\ValidationMiddleware;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\RoutedServerRequestValidator;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Mezzio\OpenApi\Middleware\Exception\RequestValidationException
 * @uses \Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException
 *
 * @covers \Kynx\Mezzio\OpenApi\Middleware\ValidationMiddleware
 */
final class ValidationMiddlewareTest extends TestCase
{
    use ValidationMiddlewareTrait;

    private ValidationMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $openApi          = $this->getOpenApi();
        $this->middleware = new ValidationMiddleware(
            $this->getResolver(),
            new RoutedServerRequestValidator($openApi),
            new ResponseValidator($openApi)
        );
    }

    public function testProcessInvalidRequestThrowsException(): void
    {
        $request = $this->getRequest('/pets/foo');

        self::expectException(RequestValidationException::class);
        self::expectExceptionMessage('Value "foo" for parameter "petId" is invalid');
        $this->middleware->process($request, new MockHandler());
    }

    public function testProcessDoesNotValidateResponse(): void
    {
        $expected   = new EmptyResponse(204);
        $request    = $this->getRequest('/pets/123');
        $middleware = new ValidationMiddleware(
            $this->getResolver(),
            new RoutedServerRequestValidator($this->getOpenApi()),
            null
        );

        $actual = $middleware->process($request, new MockHandler($expected));
        self::assertSame($expected, $actual);
    }

    public function testProcessValidatesResponse(): void
    {
        $response = new JsonResponse(['name' => 123], 200);
        $request  = $this->getRequest('/pets/123');

        self::expectException(ResponseValidationException::class);
        $this->middleware->process($request, new MockHandler($response));
    }

    public function testProcessIgnoresInvalidResponseCode(): void
    {
        $expected = new EmptyResponse(403);
        $request  = $this->getRequest('/pets/123');

        $actual = $this->middleware->process($request, new MockHandler($expected));
        self::assertSame($expected, $actual);
    }

    public function testProcessReturnsValidResponse(): void
    {
        $expected = new JsonResponse(['name' => 'Fido'], 200);
        $request  = $this->getRequest('/pets/123');

        $actual = $this->middleware->process($request, new MockHandler($expected));
        self::assertSame($expected, $actual);
    }
}
