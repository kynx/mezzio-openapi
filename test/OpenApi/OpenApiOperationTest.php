<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi;

use Kynx\Mezzio\OpenApi\OpenApiOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Mezzio\OpenApi\OpenApiOperation
 */
final class OpenApiOperationTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $operationId = 'getFoo';
        $path        = '/foo';
        $method      = 'get';
        $operation   = new OpenApiOperation($operationId, $path, $method);

        self::assertSame($operationId, $operation->getOperationId());
        self::assertSame($path, $operation->getPath());
        self::assertSame($method, $operation->getMethod());
    }
}
