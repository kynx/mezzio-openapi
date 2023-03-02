<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory;
use KynxTest\Mezzio\OpenApi\Middleware\MiddlewareTrait;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @uses \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver
 *
 * @covers \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory
 */
final class MezzioRequestFactoryResolverFactoryTest extends TestCase
{
    use MiddlewareTrait;

    public function testInvokeReturnsConfiguredResolver(): void
    {
        $pointer   = '/paths/~1pet~1{petId}/get';
        $factories = [
            $pointer => MockRequestFactory::class,
        ];
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with('config')
            ->willReturn([
                ConfigProvider::CONFIG_KEY => [
                    ConfigProvider::OPERATION_FACTORIES_KEY => $factories,
                ],
            ]);

        $factory  = new MezzioRequestFactoryResolverFactory();
        $instance = $factory($container);
        $actual   = $instance->getFactory($this->getOperationMiddlewareRequest($pointer));
        self::assertInstanceOf(MockRequestFactory::class, $actual);
    }
}
