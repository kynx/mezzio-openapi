<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

interface HydratorInterface
{
    public static function hydrate(array $data): object;

    /**
     * @template T of mixed
     * @param T $object
     * @return (T is object|array ? array : bool|float|int|string|null)
     */
    public static function extract(mixed $object): bool|array|float|int|string|null;
}
