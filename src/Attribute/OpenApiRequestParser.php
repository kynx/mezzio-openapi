<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

use Attribute;

/**
 * Associates a request parser class with an OpenApi schema
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiRequestParser
{
    public function __construct(private readonly string $jsonPointer)
    {
    }

    public function getJsonPointer(): string
    {
        return $this->jsonPointer;
    }
}
