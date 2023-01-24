<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperation;
use Kynx\Mezzio\OpenApi\Operation\OperationFactoryResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Injects open api operation data into request attributes
 *
 * Subsequent middleware and handlers can access the parsed OpenAPI operation parameters and request body via
 * `$request->getAttribute(OpenApiOperation::class)`.
 */
final class OpenApiOperationMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly OperationFactoryResolverInterface $factoryResolver)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $operation = $this->factoryResolver->getFactory($request)?->getOperation($request);
        if ($operation !== null) {
            return $handler->handle($request->withAttribute(OpenApiOperation::class, $operation));
        }
        return $handler->handle($request);
    }
}
