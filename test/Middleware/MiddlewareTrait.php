<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiRequestFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @psalm-require-extends \PHPUnit\Framework\TestCase
 */
trait MiddlewareTrait
{
    protected function getOperationMiddlewareRequest(string $pointer): ServerRequest
    {
        $middleware = $this->createStub(MiddlewareInterface::class);
        $route      = new Route('/paths/pet/{petId}/get', $middleware, ['GET'], 'pet.get');
        $route->setOptions([OpenApiRequestFactory::class => $pointer]);

        $routeResult = RouteResult::fromRoute($route);
        return (new ServerRequest())->withUri(new Uri("https://example.com/pet/123"))
            ->withAttribute(RouteResult::class, $routeResult);
    }
}
