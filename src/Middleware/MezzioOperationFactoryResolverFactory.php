<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Operation\OperationFactoryInterface;
use Psr\Container\ContainerInterface;

final class MezzioOperationFactoryResolverFactory
{
    public function __invoke(ContainerInterface $container): MezzioOperationFactoryResolver
    {
        /** @var array{mezzio-openapi: array{operation-factories: array<string, class-string<OperationFactoryInterface>>}} $config */
        $config             = $container->get('config');
        $operationFactories = $config[ConfigProvider::CONFIG_KEY][ConfigProvider::OPERATION_FACTORIES_KEY] ?? [];

        return new MezzioOperationFactoryResolver($operationFactories);
    }
}
