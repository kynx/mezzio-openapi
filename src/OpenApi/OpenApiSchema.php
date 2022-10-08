<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi;

use Attribute;

/**
 * Associates a Model class with an OpenApi schema
 *
 * @see \KynxTest\Mezzio\OpenApi\OpenApiSchemaTest
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiSchema
{
    public function __construct(private readonly string $jsonPointer)
    {
    }

    public function getJsonPointer(): string
    {
        return $this->jsonPointer;
    }
}
