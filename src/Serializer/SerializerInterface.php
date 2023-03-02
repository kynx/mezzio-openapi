<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

interface SerializerInterface
{
    public function supports(string $mimeType): bool;

    /**
     * @param class-string<HydratorInterface>|HydratorInterface|null $hydrator
     */
    public function serialize(string $mimeType, HydratorInterface|string|null $hydrator, mixed $object): string;
}
