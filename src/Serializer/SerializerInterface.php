<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

interface SerializerInterface
{
    public function supports(string $mimeType): bool;

    public function serialize(string $mimeType, array|bool|float|int|string|null $data): string;
}
