<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

use function assert;
use function is_string;

final class SecondExtractableHydrator implements HydratorInterface
{
    public static function hydrate(mixed $data): object
    {
        assert(is_string($data));
        return new SecondExtractable($data);
    }

    public static function extract(mixed $object): bool|array|float|int|string|null
    {
        assert($object instanceof SecondExtractable);
        return [
            'value' => $object->getValue(),
        ];
    }
}
