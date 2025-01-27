<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Schema;

use cebe\openapi\spec\OpenApi;
use InvalidArgumentException;
use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Schema\CacheInterface;
use Kynx\Mezzio\OpenApi\Schema\FileCache;
use Kynx\Mezzio\OpenApi\Schema\InvalidOpenApiException;
use Kynx\Mezzio\OpenApi\Schema\OpenApiFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(OpenApiFactory::class)]
#[UsesClass(FileCache::class)]
#[UsesClass(InvalidOpenApiException::class)]
final class OpenApiFactoryTest extends TestCase
{
    /** @var CacheInterface&MockObject */
    private CacheInterface $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->createMock(CacheInterface::class);
    }

    public function testGetOpenApiReadsJson(): void
    {
        $container = $this->getContainer(false);
        $factory   = new OpenApiFactory();
        $instance  = $factory($container);

        self::assertSame('JSON API', $instance->info->title);
    }

    public function testGetOpenApiReadsYaml(): void
    {
        $container = $this->getContainer(false, 'openapi.yaml');
        $factory   = new OpenApiFactory();
        $instance  = $factory($container);

        self::assertSame('YAML API', $instance->info->title);
    }

    public function testGetOpenApiInvalidDocumentThrowsException(): void
    {
        $container = $this->getContainer(false, 'malformed.yaml');
        $factory   = new OpenApiFactory();

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Cannot read specification document');
        $factory($container);
    }

    public function testGetOpenApiUnrecognizedExtensionThrowsException(): void
    {
        $container = $this->getContainer(false, 'openapi.txt');
        $factory   = new OpenApiFactory();

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage("Unrecognised schema extension 'txt'");
        $factory($container);
    }

    public function testGetOpenApiValidates(): void
    {
        $container = $this->getContainer(false, 'invalid.yaml');
        $factory   = new OpenApiFactory();

        self::expectException(InvalidOpenApiException::class);
        self::expectExceptionMessage('Invalid OpenApi document: [] OpenApi is missing required property: paths');
        $factory($container);
    }

    public function testGetOpenApiDoesNotValidate(): void
    {
        $container = $this->getContainer(false, 'invalid.yaml', false);
        $factory   = new OpenApiFactory();
        $instance  = $factory($container);

        self::assertSame('YAML API', $instance->info->title);
    }

    public function testGetCachedOpenApiReturnsCached(): void
    {
        /** @var OpenApi $expected */
        $expected = require __DIR__ . '/Asset/cached.php';
        $this->cache->method('get')
            ->willReturn($expected);

        $container = $this->getContainer(true);
        $factory   = new OpenApiFactory();
        $instance  = $factory($container);

        self::assertSame($expected, $instance);
    }

    public function testGetCachedCachesOpenApi(): void
    {
        $this->cache->method('get')
            ->willReturn(null);
        $cached = null;
        $this->cache->method('set')
            ->willReturnCallback(function (OpenApi $openApi) use (&$cached): void {
                $cached = $openApi;
            });

        $container = $this->getContainer(true);
        $factory   = new OpenApiFactory();
        $instance  = $factory($container);

        self::assertSame($cached, $instance);
    }

    private function getContainer(
        bool $cacheEnabled,
        string $document = 'openapi.json',
        bool $validate = true
    ): ContainerInterface {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')
            ->willReturnMap([
                [
                    'config',
                    [
                        ConfigProvider::CONFIG_KEY => [
                            ConfigProvider::VALIDATE_KEY => [
                                'schema' => $validate,
                            ],
                            ConfigProvider::CACHE_KEY    => [
                                'enabled' => $cacheEnabled,
                            ],
                            ConfigProvider::SCHEMA_KEY   => __DIR__ . '/Asset/' . $document,
                        ],
                    ],
                ],
                [CacheInterface::class, $this->cache],
            ]);

        return $container;
    }
}
