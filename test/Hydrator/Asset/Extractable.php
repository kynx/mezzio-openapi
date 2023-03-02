<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

final class Extractable
{
    public function __construct(private readonly string $one, private readonly int $two, private readonly bool $three)
    {
    }

    public function getOne(): string
    {
        return $this->one;
    }

    public function getTwo(): int
    {
        return $this->two;
    }

    public function isThree(): bool
    {
        return $this->three;
    }
}
