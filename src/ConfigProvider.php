<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi;

use cebe\openapi\spec\OpenApi;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolverFactory;
use Kynx\Mezzio\OpenApi\Operation\RequestFactoryResolverInterface;
use Kynx\Mezzio\OpenApi\Schema\CacheInterface;
use Kynx\Mezzio\OpenApi\Schema\FileCacheFactory;
use Kynx\Mezzio\OpenApi\Schema\OpenApiFactory;
use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializerFactory;
use Kynx\Mezzio\OpenApi\Serializer\SerializerInterface;

final class ConfigProvider
{
    public const CONFIG_KEY              = 'mezzio-openapi';
    public const SCHEMA_KEY              = 'openapi-schema';
    public const VALIDATE_KEY            = 'validate';
    public const CACHE_KEY               = 'cache';
    public const OPERATION_FACTORIES_KEY = 'operation-factories';

    /**
     * @return array{dependencies: array{factories: array<class-string, class-string>}}
     */
    public function __invoke(): array
    {
        return [
            self::CONFIG_KEY => $this->getConfig(),
            'dependencies'   => $this->getDependencies(),
        ];
    }

    public function getConfig(): array
    {
        return [
            self::VALIDATE_KEY => [
                'schema'   => true,
                'response' => true,
            ],
            self::CACHE_KEY    => [
                'enabled' => true,
                'path'    => './data/cache/openapi-cache.php',
            ],
        ];
    }

    /**
     * @return array{factories: array<class-string, class-string>}
     */
    private function getDependencies(): array
    {
        return [
            'factories' => [
                CacheInterface::class                  => FileCacheFactory::class,
                OpenApi::class                         => OpenApiFactory::class,
                OpenApiOperationMiddleware::class      => OpenApiOperationMiddlewareFactory::class,
                RequestFactoryResolverInterface::class => MezzioRequestFactoryResolverFactory::class,
                SerializerInterface::class             => DelegatingSerializerFactory::class,
            ],
        ];
    }
}
