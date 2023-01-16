<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\OpenApiOperation
 */
final class OpenApiOperationTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '#/components/schemas/Foo';
        $attribute   = new OpenApiOperation($jsonPointer);
        self::assertSame($jsonPointer, $attribute->getJsonPointer());
    }
}
