<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;
use Throwable;

use function sprintf;

final class HydrationException extends DomainException implements ClientExceptionInterface
{
    private function __construct(
        private string $target,
        private string|null $value,
        string $message,
        Throwable|null $throwable = null
    ) {
        parent::__construct($message, 400, $throwable);
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getValue(): string|null
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

    public static function fromValue(string $target, string $value): self
    {
        return new self($target, $value, sprintf(
            "Error hydrating %s with '%s'",
            $target,
            $value
        ));
    }
}
