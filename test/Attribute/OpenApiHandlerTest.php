<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiHandler;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\OpenApiHandler
 */
final class OpenApiHandlerTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '/paths/foo/get';
        $attribute   = new OpenApiHandler($jsonPointer);
        self::assertSame($jsonPointer, $attribute->getJsonPointer());
    }
}
