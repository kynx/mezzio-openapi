<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiRequestParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\OpenApiRequestParser
 */
final class OpenApiRequestParserTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '/paths/foo/get';
        $attribute   = new OpenApiRequestParser($jsonPointer);
        self::assertSame($jsonPointer, $attribute->getJsonPointer());
    }
}
