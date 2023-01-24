<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Operation\MezzioOperationFactoryResolverFactory;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockOperationFactory;
use KynxTest\Mezzio\OpenApi\Middleware\MiddlewareTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Kynx\Mezzio\OpenApi\Operation\MezzioOperationFactoryResolverFactory
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
