<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Schema;

use Kynx\Mezzio\OpenApi\ServerExceptionInterface;
use Throwable;

use function implode;

final class InvalidOpenApiException extends \RuntimeException implements ServerExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromOpenApiErrors(array $errors): self
    {
        return new self("Invalid OpenApi document: " . implode("; ", $errors), 500);
    }
}