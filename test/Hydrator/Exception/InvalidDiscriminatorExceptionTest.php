<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Exception;

use Kynx\Mezzio\OpenApi\Hydrator\Exception\InvalidDiscriminatorException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidDiscriminatorException::class)]
final class InvalidDiscriminatorExceptionTest extends TestCase
{
    public function testFromValueReturnsException(): void
    {
        $discriminator = 'foo';
        $value         = 'bar';
        $expected      = "Discriminator property '$discriminator' has invalid value '$value'";

        $exception = InvalidDiscriminatorException::fromValue($discriminator, $value);
        self::assertSame($discriminator, $exception->getDiscriminator());
        self::assertSame($value, $exception->getValue());
        self::assertSame(400, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
