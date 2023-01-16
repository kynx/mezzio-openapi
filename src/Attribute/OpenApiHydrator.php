<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

use Attribute;

/**
 * Associates hydrator with OpenAPI schema
 *
 * @see \KynxTest\Mezzio\OpenApi\Attribute\OpenApiHydratorTest
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiHydrator
{
    public function __construct(private readonly string $jsonPointer)
    {
    }

    public function getJsonPointer(): string
    {
        return $this->jsonPointer;
    }
}
