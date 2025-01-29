<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializer;
use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializerFactory;
use Kynx\Mezzio\OpenApi\Serializer\JsonSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(DelegatingSerializerFactory::class)]
#[UsesClass(DelegatingSerializer::class)]
#[UsesClass(JsonSerializer::class)]
final class DelegatingSerializerFactoryTest extends TestCase
{
    public function testInvokeReturnsConfiguredInstance(): void
    {
        $expected    = '{"foo":"bar"}';
        $object      = new stdClass();
        $object->foo = 'bar';
        $factory     = new DelegatingSerializerFactory();

        $instance = $factory();
        $actual   = $instance->serialize('application/json', ['foo' => 'bar']);
        self::assertSame($expected, $actual);
    }
}
