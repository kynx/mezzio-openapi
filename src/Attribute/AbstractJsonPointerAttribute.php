<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

/**
 * @psalm-immutable
 */
abstract class AbstractJsonPointerAttribute
{
    public function __construct(private readonly string $jsonPointer)
    {
    }

    public function getJsonPointer(): string
    {
        return $this->jsonPointer;
    }
}
