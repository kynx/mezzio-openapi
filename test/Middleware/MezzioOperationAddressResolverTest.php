<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\Middleware\MezzioOperationAddressResolver;
use Kynx\Mezzio\OpenApi\RouteOptionsUtil;
use KynxTest\Mezzio\OpenApi\MezzioRequestTrait;
use League\OpenAPIValidation\PSR7\OperationAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MezzioOperationAddressResolver::class)]
#[UsesClass(InvalidOperationException::class)]
#[UsesClass(RouteOptionsUtil::class)]
final class MezzioOperationAddressResolverTest extends TestCase
{
    use MezzioRequestTrait;

    private MezzioOperationAddressResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new MezzioOperationAddressResolver();
    }

    public function testGetOperationAddressMissingPointerThrowsException(): void
    {
        $path     = '/missing/pointer';
        $expected = "Request does not contain a pointer for '$path'";
        $request  = $this->getNonOperationRequest($path);

        self::expectException(InvalidOperationException::class);
        self::expectExceptionMessage($expected);
        $this->resolver->getOperationAddress($request);
    }

    public function testGetOperationAddressReturnsOperationAddress(): void
    {
        $expected = new OperationAddress('/pets/{petId}', 'get');
        $pointer  = '/paths/~1pets~1{petId}/get';
        $request  = $this->getOperationRequest($pointer);

        $actual = $this->resolver->getOperationAddress($request);
        self::assertEquals($expected, $actual);
    }
}
