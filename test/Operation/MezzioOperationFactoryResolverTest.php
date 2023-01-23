<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperationFactory;
use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\Operation\MezzioOperationFactoryResolver;
use KynxTest\Mezzio\OpenApi\Middleware\Asset\MockOperationFactory;
use KynxTest\Mezzio\OpenApi\Middleware\MiddlewareTrait;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \Kynx\Mezzio\OpenApi\Operation\MezzioOperationFactoryResolver
 */
final class MezzioOperationFactoryResolverTest extends TestCase
{
    use MiddlewareTrait;

    private MezzioOperationFactoryResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new MezzioOperationFactoryResolver([
            '/paths/~1pet~1{petId}/get' => MockOperationFactory::class,
        ]);
    }

    public function testGetFactoryMissingRouteOptionThrowsException(): void
    {
        $path       = '/paths/pet/123';
        $expected   = "Request does not contain a pointer for '$path'";
        $middleware = $this->createStub(MiddlewareInterface::class);
        $route      = new Route('/paths/pet/{petId}/get', $middleware, ['GET'], 'pet.get');

        $routeResult = RouteResult::fromRoute($route);
        $request     = (new ServerRequest())->withUri(new Uri("https://example.com$path"))
            ->withAttribute(RouteResult::class, $routeResult);

        self::expectException(InvalidOperationException::class);
        self::expectExceptionMessage($expected);
        $this->resolver->getFactory($request);
    }

    public function testGetFactoryMissingOperationFactoryThrowsException(): void
    {
        $pointer    = '/missing/factory';
        $expected   = "No operation factory configured for '$pointer'";
        $middleware = $this->createStub(MiddlewareInterface::class);
        $route      = new Route('/missing/factory', $middleware, ['POST'], 'pet.post');
        $route->setOptions([OpenApiOperationFactory::class => new OpenApiOperationFactory($pointer)]);

        $routeResult = RouteResult::fromRoute($route);
        $request     = (new ServerRequest())->withUri(new Uri("https://example.com/missing/factory"))
            ->withAttribute(RouteResult::class, $routeResult);

        self::expectException(InvalidOperationException::class);
        self::expectExceptionMessage($expected);
        $this->resolver->getFactory($request);
    }

    public function testGetFactoryReturnsOperationFactory(): void
    {
        $pointer = '/paths/~1pet~1{petId}/get';
        $request = $this->getOperationMiddlewareRequest($pointer);

        $actual = $this->resolver->getFactory($request);
        self::assertInstanceOf(MockOperationFactory::class, $actual);
    }
}
