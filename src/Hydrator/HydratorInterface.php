<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

interface HydratorInterface
{
    public static function hydrate(array $data): object;

    public static function extract(mixed $object): bool|array|float|int|string|null;
}
