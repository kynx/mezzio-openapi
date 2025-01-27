<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\AbstractJsonPointerAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractJsonPointerAttribute::class)]
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
