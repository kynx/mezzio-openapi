<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

use function assert;
use function is_array;

final class ExtractableHydrator implements HydratorInterface
{
    public static function hydrate(mixed $data): object
    {
        assert(is_array($data));
        return new Extractable((string) $data['one'], (int) $data['two'], (bool) $data['three']);
    }

    /**
     * @inheritDoc
     */
    public static function extract(mixed $object): array
    {
        assert($object instanceof Extractable);
        return [
            'one'   => $object->getOne(),
            'two'   => $object->getTwo(),
            'three' => $object->isThree(),
        ];
    }
}
