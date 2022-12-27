<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi;

use Attribute;

/**
 * Associates a Handler class with a specific OpenApi operation
 *
 * @see \KynxTest\Mezzio\OpenApi\OpenApiOperationTest
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiOperation
{
    public function __construct(
        private readonly ?string $operationId,
        private readonly string $path,
        private readonly string $method
    ) {
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
