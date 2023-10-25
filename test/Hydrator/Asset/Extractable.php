<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Asset;

final class Extractable
{
    public function __construct(private readonly string $one, private readonly int $two, private readonly bool $three)
    {
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getOne(): string
    {
        return $this->one;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getTwo(): int
    {
        return $this->two;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function isThree(): bool
    {
        return $this->three;
    }
}
