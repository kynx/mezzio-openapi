<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiRequestFactory;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

final class RouteOptionsUtil
{
    private function __construct()
    {
    }

    public static function getJsonPointer(ServerRequestInterface $request): ?string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        assert($routeResult instanceof RouteResult);
        $matchedRoute = $routeResult->getMatchedRoute();
        assert($matchedRoute instanceof Route);

        /** @var array{OpenApiRequestFactory::class?: string} $routeOptions */
        $routeOptions = $matchedRoute->getOptions();
        return $routeOptions[OpenApiRequestFactory::class] ?? null;
    }
}
