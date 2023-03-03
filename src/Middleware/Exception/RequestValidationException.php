<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;
use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Throwable;

use function sprintf;

final class RequestValidationException extends DomainException implements ClientExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function validationFailed(ValidationFailed $exception): self
    {
        if ($exception instanceof RequiredParameterMissing) {
            return new self(sprintf("Parameter '%s' is required", $exception->name()), 400, $exception);
        }

        return new self($exception->getMessage(), 400, $exception);
    }
}
