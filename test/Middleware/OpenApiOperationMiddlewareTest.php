<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiRequest;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Operation\RequestFactoryResolverInterface;
use KynxTest\Mezzio\OpenApi\Middleware\MockHandler;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockOperation;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(OpenApiOperationMiddleware::class)]
final class OpenApiOperationMiddlewareTest extends TestCase
{
    public function testHandleAddsOpenApiOperationToRequest(): void
    {
        $handler  = new MockHandler();
        $resolver = $this->createStub(RequestFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn(new MockRequestFactory());
        $middleware = new OpenApiOperationMiddleware($resolver);

        $middleware->process(new ServerRequest(), $handler);
        $handledRequest = $handler->getHandledRequest();
        self::assertInstanceOf(ServerRequestInterface::class, $handledRequest);
        $actual = $handledRequest->getAttribute(OpenApiRequest::class);
        self::assertInstanceOf(MockOperation::class, $actual);
    }

    public function testHandleNoFactoryHandlesRequest(): void
    {
        $expected = new ServerRequest();
        $handler  = new MockHandler();
        $resolver = $this->createStub(RequestFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn(null);
        $middleware = new OpenApiOperationMiddleware($resolver);

        $middleware->process($expected, $handler);
        $actual = $handler->getHandledRequest();
        self::assertSame($expected, $actual);
    }
}
