<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializerFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @uses \Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializer
 * @uses \Kynx\Mezzio\OpenApi\Serializer\JsonSerializer
 *
 * @covers \Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializerFactory
 */
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
