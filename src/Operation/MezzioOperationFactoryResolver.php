<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperationFactory;
use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\Operation\OperationFactoryInterface;
use Kynx\Mezzio\OpenApi\Operation\OperationFactoryResolverInterface;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

final class MezzioOperationFactoryResolver implements OperationFactoryResolverInterface
{
    /**
     * @param array<string, class-string> $operationFactories
     */
    public function __construct(private readonly array $operationFactories)
    {
    }

    public function getFactory(ServerRequestInterface $request): OperationFactoryInterface|null
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        assert($routeResult instanceof RouteResult);
        $matchedRoute = $routeResult->getMatchedRoute();
        assert($matchedRoute instanceof Route);

        $routeOptions     = $matchedRoute->getOptions();
        $operationFactory = $routeOptions[OpenApiOperationFactory::class] ?? null;
        if (! $operationFactory instanceof OpenApiOperationFactory) {
            throw InvalidOperationException::missingPointer($request->getUri()->getPath());
        }

        /** @var class-string<OperationFactoryInterface>|null $factoryClass */
        $factoryClass = $this->operationFactories[$operationFactory->getJsonPointer()] ?? null;
        if ($factoryClass === null) {
            throw InvalidOperationException::missingOperationFactory($operationFactory->getJsonPointer());
        }

        return new $factoryClass();
    }
}
