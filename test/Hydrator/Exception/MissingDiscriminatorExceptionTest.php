<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Exception;

use Kynx\Mezzio\OpenApi\Hydrator\Exception\MissingDiscriminatorException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MissingDiscriminatorException::class)]
final class MissingDiscriminatorExceptionTest extends TestCase
{
    public function testFromMissingReturnsException(): void
    {
        $property      = 'foo';
        $discriminator = 'bar';
        $expected      = "Property '$property' is missing discriminator '$discriminator'";

        $exception = MissingDiscriminatorException::fromMissing($property, $discriminator);
        self::assertSame($property, $exception->getProperty());
        self::assertSame($discriminator, $exception->getDiscriminator());
        self::assertSame(400, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
