<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver;
use KynxTest\Mezzio\OpenApi\MezzioRequestTrait;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException
 * @uses \Kynx\Mezzio\OpenApi\RouteOptionsUtil
 *
 * @covers \Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver
 */
final class MezzioRequestFactoryResolverTest extends TestCase
{
    use MezzioRequestTrait;

    private MezzioRequestFactoryResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new MezzioRequestFactoryResolver([
            '/paths/~1pet~1{petId}/get' => MockRequestFactory::class,
        ]);
    }

    public function testGetFactoryMissingRouteOptionThrowsException(): void
    {
        $path     = '/pet/123';
        $expected = "Request does not contain a pointer for '$path'";
        $request  = $this->getNonOperationRequest($path);

        self::expectException(InvalidOperationException::class);
        self::expectExceptionMessage($expected);
        $this->resolver->getFactory($request);
    }

    public function testGetFactoryMissingOperationFactoryThrowsException(): void
    {
        $pointer  = '/missing/factory';
        $expected = "No request factory configured for '$pointer'";
        $request  = $this->getOperationRequest($pointer);

        self::expectException(InvalidOperationException::class);
        self::expectExceptionMessage($expected);
        $this->resolver->getFactory($request);
    }

    public function testGetFactoryReturnsOperationFactory(): void
    {
        $pointer = '/paths/~1pet~1{petId}/get';
        $request = $this->getOperationRequest($pointer);

        $actual = $this->resolver->getFactory($request);
        self::assertInstanceOf(MockRequestFactory::class, $actual);
    }
}
