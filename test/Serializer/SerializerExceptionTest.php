<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Serializer;

use Exception;
use Kynx\Mezzio\OpenApi\Serializer\SerializerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SerializerException::class)]
final class SerializerExceptionTest extends TestCase
{
    public function testUnsupportedMimeTypeSetsMessageAndCode(): void
    {
        $expected  = "Unsupported mime type 'foo/bar'";
        $exception = SerializerException::unsupportedMimeType('foo/bar');
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }

    public function testFromThrowableSetsMessageAndCode(): void
    {
        $expected  = "Serialization error: Foo";
        $exception = SerializerException::fromThrowable(new Exception('Foo'));
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
