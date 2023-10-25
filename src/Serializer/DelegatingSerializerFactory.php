<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

final class DelegatingSerializerFactory
{
    public function __invoke(): DelegatingSerializer
    {
        return new DelegatingSerializer(new JsonSerializer());
    }
}
