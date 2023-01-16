<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator;

use Exception;
use Kynx\Mezzio\OpenApi\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Hydrator\HydratorException
 */
final class HydratorExceptionTest extends TestCase
{
    public function testMissingDiscriminatorProperty(): void
    {
        $parent    = 'foo';
        $property  = 'bar';
        $expected  = "Property '$parent' is missing discriminator property '$property'";
        $exception = HydratorException::missingDiscriminatorProperty($parent, $property);
        self::assertSame($expected, $exception->getMessage());
        self::assertSame(400, $exception->getCode());
    }

    public function testInvalidDiscriminatorValue(): void
    {
        $property  = 'foo';
        $value     = 'bar';
        $expected  = "Discriminator property '$property' has invalid value '$value'";
        $exception = HydratorException::invalidDiscriminatorValue($property, $value);
        self::assertSame($expected, $exception->getMessage());
        self::assertSame(400, $exception->getCode());
    }

    public function testFromThrowable(): void
    {
        $previous  = new Exception("Foo error", 999);
        $message   = "Cannot hydrate " . self::class . ": " . $previous->getMessage();
        $exception = HydratorException::fromThrowable(self::class, $previous);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(400, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
