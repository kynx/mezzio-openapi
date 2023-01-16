<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Attribute;

use Kynx\Mezzio\OpenApi\Attribute\OpenApiModel;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\Attribute\OpenApiModel
 */
final class OpenApiModelTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '/components/schemas/Pet';
        $schema      = new OpenApiModel($jsonPointer);

        self::assertSame($jsonPointer, $schema->getJsonPointer());
    }
}
