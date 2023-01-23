<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperation;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Middleware\OperationFactoryResolverInterface;
use KynxTest\Mezzio\OpenApi\Middleware\Asset\MockOperation;
use KynxTest\Mezzio\OpenApi\Middleware\Asset\MockOperationFactory;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware
 */
final class OpenApiOperationMiddlewareTest extends TestCase
{
    public function testHandleAddsOpenApiOperationToRequest(): void
    {
        $handler        = $this->createStub(RequestHandlerInterface::class);
        $handledRequest = null;
        $handler->method('handle')
            ->willReturnCallback(function (ServerRequestInterface $request) use (&$handledRequest): EmptyResponse {
                $handledRequest = $request;
                return new EmptyResponse();
            });
        $resolver = $this->createStub(OperationFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn(new MockOperationFactory());
        $middleware = new OpenApiOperationMiddleware($resolver);

        $middleware->process(new ServerRequest(), $handler);
        self::assertInstanceOf(ServerRequestInterface::class, $handledRequest);
        $actual = $handledRequest->getAttribute(OpenApiOperation::class);
        self::assertInstanceOf(MockOperation::class, $actual);
    }
}
