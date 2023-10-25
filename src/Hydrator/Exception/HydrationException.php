<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;
use Throwable;

use function get_debug_type;
use function is_scalar;
use function sprintf;

final class HydrationException extends DomainException implements ClientExceptionInterface
{
    private function __construct(
        private string $target,
        private bool|int|float|string|null $value,
        string $message,
        Throwable|null $throwable = null
    ) {
        parent::__construct($message, 400, $throwable);
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getValue(): bool|int|float|string|null
    {
        return $this->value;
    }

    public static function fromThrowable(string $target, Throwable $throwable): self
    {
        return new self($target, null, sprintf(
            "Cannot hydrate %s: %s",
            $target,
            $throwable->getMessage()
        ), $throwable);
    }

    public static function fromValue(string $target, mixed $value): self
    {
        $value = is_scalar($value) ? $value : get_debug_type($value);
        return new self($target, $value, sprintf(
            "Error hydrating %s with '%s'",
            $target,
            (string) $value
        ));
    }
}
