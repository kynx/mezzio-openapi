<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Schema;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Psr\Container\ContainerInterface;

use function assert;

final class FileCacheFactory
{
    public function __invoke(ContainerInterface $container): FileCache
    {
        $path = (string) (
            $container->get('config')[ConfigProvider::CONFIG_KEY][ConfigProvider::CACHE_KEY]['path'] ?? ''
        );
        assert($path !== '');

        return new FileCache($path);
    }
}
