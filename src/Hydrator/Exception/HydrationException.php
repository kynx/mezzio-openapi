<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;
use Throwable;

use function sprintf;

final class HydrationException extends DomainException implements ClientExceptionInterface
{
    private function __construct(private string $target, string $message, Throwable|null $throwable = null)
    {
        parent::__construct($message, 400, $throwable);
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public static function fromThrowable(string $target, Throwable $throwable): self
    {
        return new self($target, sprintf(
            "Cannot hydrate %s: %s",
            $target,
            $throwable->getMessage()
        ), $throwable);
    }
}
