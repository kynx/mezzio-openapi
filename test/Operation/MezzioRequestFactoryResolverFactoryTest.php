<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory;
use KynxTest\Mezzio\OpenApi\MezzioRequestTrait;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @uses \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver
 * @uses \Kynx\Mezzio\OpenApi\RouteOptionsUtil
 *
 * @covers \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory
 */
final class MezzioRequestFactoryResolverFactoryTest extends TestCase
{
    use MezzioRequestTrait;

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
        $actual   = $instance->getFactory($this->getOperationRequest($pointer));
        self::assertInstanceOf(MockRequestFactory::class, $actual);
    }
}
