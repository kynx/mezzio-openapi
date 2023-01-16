<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

use Attribute;

/**
 * Associates an operation class with an OpenApi schema
 *
 * @see \KynxTest\Mezzio\OpenApi\OpenApiOperationTest
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiOperation
{
    public function __construct(private readonly string $jsonPointer)
    {
    }

    public function getJsonPointer(): string
    {
        return $this->jsonPointer;
    }
}
