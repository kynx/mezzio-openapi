<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

interface SerializerInterface
{
    public function supports(string $mimeType): bool;

    /**
     * @param array|bool|float|int|string|null $hydrator
     */
    public function serialize(string $mimeType, array|bool|float|int|string|null $data): string;
}
