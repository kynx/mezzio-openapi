<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Exception;

use Kynx\Mezzio\OpenApi\Operation\Exception\InvalidAcceptException;
use Negotiation\Exception\InvalidArgument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidAcceptException::class)]
final class InvalidAcceptExceptionTest extends TestCase
{
    public function testFromNegotiationExceptionSetsMessageAndCode(): void
    {
        $expected  = "Error negotiating Accept header: foo/bar";
        $exception = InvalidAcceptException::fromNegotiationException(new InvalidArgument("foo/bar"));
        self::assertSame(415, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }

    public function testUnsupportedMimeTypeSetsMessageAndCode(): void
    {
        $expected  = "Unsupported mime type 'foo/bar'";
        $exception = InvalidAcceptException::unsupportedMimeType('foo/bar');
        self::assertSame(415, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
