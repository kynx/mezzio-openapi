<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory;
use Kynx\Mezzio\OpenApi\Operation\RequestFactoryResolverInterface;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @uses \Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware
 *
 * @covers \Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory
 */
final class OpenApiOperationMiddlewareFactoryTest extends TestCase
{
    public function testInvokeReturnsConfiguredInstance(): void
    {
        $expected = new EmptyResponse();
        $handler  = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($expected);

        $factory  = new MockRequestFactory();
        $resolver = $this->createMock(RequestFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn($factory);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with(RequestFactoryResolverInterface::class)
            ->willReturn($resolver);

        $factory    = new OpenApiOperationMiddlewareFactory();
        $middleware = $factory($container);
        $actual     = $middleware->process(new ServerRequest(), $handler);
        self::assertSame($expected, $actual);
    }
}
