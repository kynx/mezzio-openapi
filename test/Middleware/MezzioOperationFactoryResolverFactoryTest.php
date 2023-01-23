<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Middleware\MezzioOperationFactoryResolverFactory;
use KynxTest\Mezzio\OpenApi\Middleware\Asset\MockOperationFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Kynx\Mezzio\OpenApi\Middleware\MezzioOperationFactoryResolverFactory
 */
final class MezzioOperationFactoryResolverFactoryTest extends TestCase
{
    use MiddlewareTrait;

    public function testInvokeReturnsConfiguredResolver(): void
    {
        $pointer   = '/paths/~1pet~1{petId}/get';
        $factories = [
            $pointer => MockOperationFactory::class,
        ];
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with('config')
            ->willReturn([
                ConfigProvider::CONFIG_KEY => [
                    ConfigProvider::OPERATION_FACTORIES_KEY => $factories,
                ],
            ]);

        $factory  = new MezzioOperationFactoryResolverFactory();
        $instance = $factory($container);
        $actual   = $instance->getFactory($this->getOperationMiddlewareRequest($pointer));
        self::assertInstanceOf(MockOperationFactory::class, $actual);
    }
}
