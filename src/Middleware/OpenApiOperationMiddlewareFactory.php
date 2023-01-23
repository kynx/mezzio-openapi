<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Operation\OperationFactoryResolverInterface;
use Psr\Container\ContainerInterface;

final class OpenApiOperationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): OpenApiOperationMiddleware
    {
        return new OpenApiOperationMiddleware($container->get(OperationFactoryResolverInterface::class));
    }
}
