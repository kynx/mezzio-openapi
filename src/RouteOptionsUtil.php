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
    /**
     * @psalm-suppress UnusedConstructor
     */
    private function __construct()
    {
    }

    public static function getJsonPointer(ServerRequestInterface $request): ?string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        assert($routeResult instanceof RouteResult);
        $matchedRoute = $routeResult->getMatchedRoute();
        assert($matchedRoute instanceof Route);

        $routeOptions = $matchedRoute->getOptions();
        if (isset($routeOptions[OpenApiRequestFactory::class])) {
            return (string) $routeOptions[OpenApiRequestFactory::class];
        }
        return null;
    }
}
