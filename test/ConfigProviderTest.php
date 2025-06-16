<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi;

use Generator;
use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory;
use Kynx\Mezzio\OpenApi\Schema\FileCache;
use Kynx\Mezzio\OpenApi\Schema\FileCacheFactory;
use Kynx\Mezzio\OpenApi\Schema\OpenApiFactory;
use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializer;
use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializerFactory;
use Kynx\Mezzio\OpenApi\Serializer\JsonSerializer;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function array_keys;
use function sys_get_temp_dir;

#[CoversClass(ConfigProvider::class)]
#[UsesClass(DelegatingSerializer::class)]
#[UsesClass(DelegatingSerializerFactory::class)]
#[UsesClass(FileCache::class)]
#[UsesClass(FileCacheFactory::class)]
#[UsesClass(JsonSerializer::class)]
#[UsesClass(MezzioRequestFactoryResolver::class)]
#[UsesClass(MezzioRequestFactoryResolverFactory::class)]
#[UsesClass(OpenApiFactory::class)]
#[UsesClass(OpenApiOperationMiddleware::class)]
#[UsesClass(OpenApiOperationMiddlewareFactory::class)]
final class ConfigProviderTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $config                             = (new ConfigProvider())();
        $config[ConfigProvider::CONFIG_KEY] = [
            ConfigProvider::SCHEMA_KEY => __DIR__ . '/Schema/Asset/openapi.json',
            ConfigProvider::CACHE_KEY  => [
                'enabled' => false,
                'path'    => sys_get_temp_dir() . '/openapi-cache.php',
            ],
        ];

        $dependencies                       = $config['dependencies'];
        $dependencies['services']['config'] = $config;

        /** @psalm-suppress ArgumentTypeCoercion */
        $this->container = new ServiceManager($dependencies);
    }

    /**
     * @param class-string $dependency
     */
    #[DataProvider('dependencyProvider')]
    public function testDependenciesResolve(string $dependency): void
    {
        $actual = $this->container->get($dependency);
        self::assertInstanceOf($dependency, $actual);
    }

    /**
     * @return Generator<class-string, array{0: class-string}>
     */
    public static function dependencyProvider(): Generator
    {
        $config = (new ConfigProvider())();
        foreach (array_keys($config['dependencies']['factories']) as $dependency) {
            yield $dependency => [$dependency];
        }
    }
}
