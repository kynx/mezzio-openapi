<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi;

use Kynx\Mezzio\OpenApi\OpenApiSchema;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\OpenApiSchema
 */
final class OpenApiSchemaTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $jsonPointer = '/components/schemas/Pet';
        $schema      = new OpenApiSchema($jsonPointer);

        self::assertSame($jsonPointer, $schema->getJsonPointer());
    }
}
