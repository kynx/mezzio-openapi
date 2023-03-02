<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiRequestFactory;
use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

final class MezzioRequestFactoryResolver implements RequestFactoryResolverInterface
{
    /**
     * @param array<string, class-string> $operationFactories
     */
    public function __construct(private readonly array $operationFactories)
    {
    }

    public function getFactory(ServerRequestInterface $request): RequestFactoryInterface|null
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        assert($routeResult instanceof RouteResult);
        $matchedRoute = $routeResult->getMatchedRoute();
        assert($matchedRoute instanceof Route);

        /** @var array{OpenApiRequestFactory::class?: string} $routeOptions */
        $routeOptions = $matchedRoute->getOptions();
        $jsonPointer  = $routeOptions[OpenApiRequestFactory::class] ?? null;
        if ($jsonPointer === null) {
            throw InvalidOperationException::missingPointer($request->getUri()->getPath());
        }

        /** @var class-string<RequestFactoryInterface>|null $factoryClass */
        $factoryClass = $this->operationFactories[$jsonPointer] ?? null;
        if ($factoryClass === null) {
            throw InvalidOperationException::missingRequestFactory($jsonPointer);
        }

        return new $factoryClass();
    }
}
