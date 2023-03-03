<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\Exception\RequestValidationException;
use Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\RoutedServerRequestValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private OperationAddressResolverInterface $operationAddressResolver,
        private RoutedServerRequestValidator $requestValidator,
        private ?ResponseValidator $responseValidator
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $operationAddress = $this->operationAddressResolver->getOperationAddress($request);
        try {
            $this->requestValidator->validate($operationAddress, $request);
        } catch (ValidationFailed $exception) {
            throw RequestValidationException::validationFailed($exception);
        }

        if ($this->responseValidator === null) {
            return $handler->handle($request);
        }

        $response = $handler->handle($request);
        try {
            $this->responseValidator->validate($operationAddress, $response);
        } catch (ValidationFailed $exception) {
            throw ResponseValidationException::validationFailed($exception);
        }

        return $response;
    }
}
