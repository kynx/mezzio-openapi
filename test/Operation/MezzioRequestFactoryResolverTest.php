<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\Operation\MezzioRequestFactoryResolver;
use Kynx\Mezzio\OpenApi\RouteOptionsUtil;
use KynxTest\Mezzio\OpenApi\MezzioRequestTrait;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockRequestFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MezzioRequestFactoryResolver::class)]
#[UsesClass(InvalidOperationException::class)]
#[UsesClass(RouteOptionsUtil::class)]
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

    public function testGetFactoryMissingOperationFactoryReturnsNull(): void
    {
        $pointer = '/missing/factory';
        $request = $this->getOperationRequest($pointer);

        $actual = $this->resolver->getFactory($request);
        self::assertNull($actual);
    }

    public function testGetFactoryReturnsOperationFactory(): void
    {
        $pointer = '/paths/~1pet~1{petId}/get';
        $request = $this->getOperationRequest($pointer);

        $actual = $this->resolver->getFactory($request);
        self::assertInstanceOf(MockRequestFactory::class, $actual);
    }
}
