<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiRequestFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @psalm-require-extends TestCase
 */
trait MezzioRequestTrait
{
    protected function getOperationRequest(string $pointer): ServerRequest
    {
        $middleware = $this->createStub(MiddlewareInterface::class);
        $route      = new Route('/pet/{petId:\d+}/get', $middleware, ['GET'], 'pet.get');
        $route->setOptions([OpenApiRequestFactory::class => $pointer]);
        $routeResult = RouteResult::fromRoute($route);

        return (new ServerRequest())->withUri(new Uri("https://example.com/pet/123"))
            ->withAttribute(RouteResult::class, $routeResult);
    }

    protected function getNonOperationRequest(string $path): ServerRequest
    {
        $middleware  = $this->createStub(MiddlewareInterface::class);
        $route       = new Route('/pet/{petId:\d+}/get', $middleware, ['GET'], 'pet.get');
        $routeResult = RouteResult::fromRoute($route);

        return (new ServerRequest())->withUri(new Uri("https://example.com$path"))
            ->withAttribute(RouteResult::class, $routeResult);
    }
}
