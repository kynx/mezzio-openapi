<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Schema;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Schema\FileCacheFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @uses \Kynx\Mezzio\OpenApi\Schema\FileCache
 *
 * @covers \Kynx\Mezzio\OpenApi\Schema\FileCacheFactory
 */
final class FileCacheFactoryTest extends TestCase
{
    use FileCacheTrait;

    public function testInvokeReturnsConfiguredInstance(): void
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')
            ->willReturnMap([
                ['config', [
                    ConfigProvider::CONFIG_KEY => [
                        ConfigProvider::CACHE_KEY => [
                            'path' => $this->getCacheFileName(),
                        ],
                    ],
                ]],
            ]);

        $factory  = new FileCacheFactory();
        $instance = $factory($container);
        $instance->set($this->getOpenApi());
        self::assertFileExists($this->getCacheFileName());
    }
}
