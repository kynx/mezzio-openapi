<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\AbstractJsonPointerAttribute;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\AbstractJsonPointerAttribute
 */
final class AbstractJsonPointerAttributeTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '/components/schemas/Pet';
        /** @psalm-suppress MissingImmutableAnnotation */
        $attribute = new class ($jsonPointer) extends AbstractJsonPointerAttribute {
        };

        self::assertSame($jsonPointer, $attribute->getJsonPointer());
    }
}
