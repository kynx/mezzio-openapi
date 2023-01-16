<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

interface HydratorInterface
{
    public static function hydrate(array $data): object;
}
