<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiHydrator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\OpenApiHydrator
 */
final class OpenApiHydratorTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '#/components/schemas/Foo';
        $attribute   = new OpenApiHydrator($jsonPointer);
        self::assertSame($jsonPointer, $attribute->getJsonPointer());
    }
}
