<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperation;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Operation\OperationFactoryResolverInterface;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockOperation;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockOperationFactory;
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
        $handler  = $this->getHandler($handledRequest);
        $resolver = $this->createStub(OperationFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn(new MockOperationFactory());
        $middleware = new OpenApiOperationMiddleware($resolver);

        $middleware->process(new ServerRequest(), $handler);
        self::assertInstanceOf(ServerRequestInterface::class, $handledRequest);
        $actual = $handledRequest->getAttribute(OpenApiOperation::class);
        self::assertInstanceOf(MockOperation::class, $actual);
    }

    public function testHandleNoFactoryHandlesRequest(): void
    {
        $expected = new ServerRequest();
        $handler  = $this->getHandler($actual);
        $resolver = $this->createStub(OperationFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn(null);
        $middleware = new OpenApiOperationMiddleware($resolver);

        $middleware->process($expected, $handler);
        self::assertSame($expected, $actual);
    }

    private function getHandler(ServerRequest|null &$handledRequest): RequestHandlerInterface
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturnCallback(function (ServerRequestInterface $request) use (&$handledRequest): EmptyResponse {
                $handledRequest = $request;
                return new EmptyResponse();
            });
        return $handler;
    }
}
