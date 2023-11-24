<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

final class SecondExtractable
{
    public function __construct(private readonly string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
