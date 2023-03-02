<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

use Psr\Container\ContainerInterface;

final class DelegatingSerializerFactory
{
    public function __invoke(ContainerInterface $container): DelegatingSerializer
    {
        return new DelegatingSerializer(new JsonSerializer());
    }
}
