<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;
use TypeError;

final class TypeErrorHydrator implements HydratorInterface
{
    public static function hydrate(array $data): object
    {
        throw new TypeError('Bad type');
    }

    public static function extract(mixed $object): bool|array|float|int|string|null
    {
        return [];
    }
}
