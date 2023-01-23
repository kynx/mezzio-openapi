<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi;

use Kynx\Mezzio\OpenApi\Middleware\MezzioOperationFactoryResolverFactory;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddleware;
use Kynx\Mezzio\OpenApi\Middleware\OpenApiOperationMiddlewareFactory;
use Kynx\Mezzio\OpenApi\Middleware\OperationFactoryResolverInterface;

final class ConfigProvider
{
    public const CONFIG_KEY              = 'mezzio-openapi';
    public const OPERATION_FACTORIES_KEY = 'operation-factories';

    /**
     * @return array{dependencies: array{factories: array<class-string, class-string>}}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array{factories: array<class-string, class-string>}
     */
    private function getDependencies(): array
    {
        return [
            'factories' => [
                OpenApiOperationMiddleware::class        => OpenApiOperationMiddlewareFactory::class,
                OperationFactoryResolverInterface::class => MezzioOperationFactoryResolverFactory::class,
            ],
        ];
    }
}
