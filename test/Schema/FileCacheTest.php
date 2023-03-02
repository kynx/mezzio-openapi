<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Schema;

use cebe\openapi\spec\OpenApi;
use Kynx\Mezzio\OpenApi\Schema\FileCache;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Schema\FileCache
 */
final class FileCacheTest extends TestCase
{
    use FileCacheTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new FileCache($this->getCacheFileName());
    }

    public function testGetNonExistentReturnsNull(): void
    {
        $actual = $this->cache->get();
        self::assertNull($actual);
    }

    public function testGetReturnsOpenApi(): void
    {
        $expected = $this->getOpenApi();
        $this->createCache();

        $actual = $this->cache->get();
        self::assertEquals($expected, $actual);
    }

    public function testSetCreatesCache(): void
    {
        $openApi = $this->getOpenApi();
        $file    = $this->getCacheFileName();
        $this->cache->set($openApi);

        self::assertFileExists($file);
        /**
         * @var OpenApi $cached
         * @psalm-suppress UnresolvableInclude
         */
        $cached = require $file;
        self::assertEquals($openApi, $cached);
    }

    public function testSetUnwritableIgnoresException(): void
    {
        $file = __DIR__ . '/nonexistentdirectory/cache';
        $cache = new FileCache($file);
        $cache->set($this->getOpenApi());
        self::assertFileDoesNotExist($file);
    }

    public function testClearRemovesCache(): void
    {
        $this->createCache();
        $this->cache->clear();
        self::assertFileDoesNotExist($this->getCacheFileName());
    }

    private function createCache(): void
    {
        copy(__DIR__ . '/Asset/cached.php', $this->getCacheFileName());
    }
}
