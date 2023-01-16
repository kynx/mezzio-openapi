<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

final class GoodHydrator implements HydratorInterface
{
    public static function hydrate(array $data): object
    {
        return (object) $data;
    }
}
