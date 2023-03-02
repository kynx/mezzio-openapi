<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi;

use Generator;
use Kynx\Mezzio\OpenApi\ConfigProvider;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function array_keys;

/**
 * @uses \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver
 * @uses \Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware
 * @uses \Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory
 * @uses \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver
 * @uses \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory
 * @uses \Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializer
 * @uses \Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializerFactory
 * @uses \Kynx\Mezzio\OpenApi\Serializer\JsonSerializer
 *
 * @covers \Kynx\Mezzio\OpenApi\ConfigProvider
 */
final class ConfigProviderTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $config                             = (new ConfigProvider())();
        $dependencies                       = $config['dependencies'];
        $dependencies['services']['config'] = $config;

        /** @psalm-suppress InvalidArgument */
        $this->container = new ServiceManager($dependencies);
    }

    /**
     * @dataProvider dependencyProvider
     * @param class-string $dependency
     */
    public function testDependenciesResolve(string $dependency): void
    {
        $actual = $this->container->get($dependency);
        self::assertInstanceOf($dependency, $actual);
    }

    public function dependencyProvider(): Generator
    {
        $config = (new ConfigProvider())();
        foreach (array_keys($config['dependencies']['factories']) as $dependency) {
            yield $dependency => [$dependency];
        }
    }
}
