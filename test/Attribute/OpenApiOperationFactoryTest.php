<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiOperationFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\OpenApiOperationFactory
 */
final class OpenApiOperationFactoryTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '/paths/foo/get';
        $attribute   = new OpenApiOperationFactory($jsonPointer);
        self::assertSame($jsonPointer, $attribute->getJsonPointer());
    }
}
