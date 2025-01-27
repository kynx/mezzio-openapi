<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator\Exception;

use Kynx\Mezzio\OpenApi\Hydrator\Exception\ExtractionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ExtractionException::class)]
final class ExtractionExceptionTest extends TestCase
{
    public function testInvalidObjectReturnsException(): void
    {
        $expected  = "Cannot extract stdClass: expected object of type Foo";
        $exception = ExtractionException::invalidObject(new stdClass(), 'Foo');
        self::assertSame(500, $exception->getCode());
        self::assertSame($expected, $exception->getMessage());
    }
}
