<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;
use stdClass;

final class BadHydrator implements HydratorInterface
{
    public static function hydrate(mixed $data): object
    {
        return new stdClass();
    }

    public static function extract(mixed $object): bool|array|float|int|string|null
    {
        return [];
    }
}
