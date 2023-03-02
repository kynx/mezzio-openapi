<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\ConfigProvider;
use Psr\Container\ContainerInterface;

final class MezzioRequestFactoryResolverFactory
{
    public function __invoke(ContainerInterface $container): MezzioRequestFactoryResolver
    {
        /** @var array{mezzio-openapi: array{operation-factories: array<string, class-string<RequestFactoryInterface>>}} $config */
        $config             = $container->get('config');
        $operationFactories = $config[ConfigProvider::CONFIG_KEY][ConfigProvider::OPERATION_FACTORIES_KEY] ?? [];

        return new MezzioRequestFactoryResolver($operationFactories);
    }
}
