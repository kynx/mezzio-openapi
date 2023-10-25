<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

use function get_object_vars;
use function is_object;

final class GoodHydrator implements HydratorInterface
{
    public static function hydrate(mixed $data): object
    {
        return (object) $data;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function extract(mixed $object): bool|array|float|int|string|null
    {
        if (is_object($object)) {
            return get_object_vars($object);
        }
        return $object;
    }
}
