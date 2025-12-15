<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory;
use Kynx\Mezzio\OpenApi\Operation\RequestFactoryResolverInterface;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(OpenApiOperationMiddlewareFactory::class)]
#[UsesClass(OpenApiOperationMiddleware::class)]
final class OpenApiOperationMiddlewareFactoryTest extends TestCase
{
    public function testInvokeReturnsConfiguredInstance(): void
    {
        $expected = new EmptyResponse();
        $handler  = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($expected);

        $factory  = new MockRequestFactory();
        $resolver = self::createStub(RequestFactoryResolverInterface::class);
        $resolver->method('getFactory')
            ->willReturn($factory);

        $container = self::createStub(ContainerInterface::class);
        $container->method('get')
            ->with(RequestFactoryResolverInterface::class)
            ->willReturn($resolver);

        $factory    = new OpenApiOperationMiddlewareFactory();
        $middleware = $factory($container);
        $actual     = $middleware->process(new ServerRequest(), $handler);
        self::assertSame($expected, $actual);
    }
}
