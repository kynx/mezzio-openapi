<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ServerExceptionInterface;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Throwable;

final class ResponseValidationException extends DomainException implements ServerExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function validationFailed(ValidationFailed $exception): self
    {
        return new self("Failed to create a valid response", 500, $exception);
    }
}
