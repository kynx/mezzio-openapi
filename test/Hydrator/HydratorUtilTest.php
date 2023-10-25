<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator;

use DateTimeImmutable;
use Kynx\Mezzio\OpenApi\Hydrator\DateTimeImmutableHydrator;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\InvalidDiscriminatorException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\MissingDiscriminatorException;
use Kynx\Mezzio\OpenApi\Hydrator\HydratorUtil;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\Extractable;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\GoodHydrator;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\MockEnum;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\TypeErrorHydrator;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @uses \Kynx\Mezzio\OpenApi\Hydrator\DateTimeImmutableHydrator
 * @uses \Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException
 * @uses \Kynx\Mezzio\OpenApi\Hydrator\Exception\InvalidDiscriminatorException
 * @uses \Kynx\Mezzio\OpenApi\Hydrator\Exception\MissingDiscriminatorException
 *
 * @covers \Kynx\Mezzio\OpenApi\Hydrator\HydratorUtil
 */
final class HydratorUtilTest extends TestCase
{
    public function testHydrateDiscriminatorValuesMissingPropertyThrowsException(): void
    {
        $data     = [
            'foo' => [
                'bar' => 'a',
            ],
        ];
        $valueMap = [
            'foo' => [
                'key' => 'baz',
                'map' => [],
            ],
        ];

        self::expectException(MissingDiscriminatorException::class);
        self::expectExceptionMessage("Property 'foo' is missing discriminator 'baz'");
        HydratorUtil::hydrateDiscriminatorValues($data, [], $valueMap);
    }

    public function testHydrateDiscriminatorValuesMissingKeyThrowsException(): void
    {
        $data     = [
            'foo' => [
                'bar' => 'a',
            ],
        ];
        $valueMap = [
            'foo' => [
                'key' => 'bar',
                'map' => [
                    'b' => GoodHydrator::class,
                ],
            ],
        ];

        self::expectException(InvalidDiscriminatorException::class);
        self::expectExceptionMessage("Discriminator property 'bar' has invalid value 'a'");
        HydratorUtil::hydrateDiscriminatorValues($data, [], $valueMap);
    }

    public function testHydrateDiscriminatorValuesTypeErrorThrowsException(): void
    {
        $data     = [
            'foo' => [
                'bar' => 'a',
            ],
        ];
        $valueMap = [
            'foo' => [
                'key' => 'bar',
                'map' => [
                    'a' => TypeErrorHydrator::class,
                ],
            ],
        ];

        self::expectException(HydrationException::class);
        self::expectExceptionMessage("Bad type");
        HydratorUtil::hydrateDiscriminatorValues($data, [], $valueMap);
    }

    public function testHydrateDiscriminatorValuesIgnoresMissingData(): void
    {
        $expected = [
            'skip' => "don't hydrate me",
        ];
        $valueMap = [
            'foo' => [
                'key' => 'bar',
                'map' => ['a' => Asset\BadHydrator::class],
            ],
        ];

        $actual = HydratorUtil::hydrateDiscriminatorValues($expected, [], $valueMap);
        self::assertSame($expected, $actual);
    }

    public function testHydrateDiscriminatorValuesHydrates(): void
    {
        $expected = [
            'skip' => "don't hydrate me",
            'foo'  => (object) ['bar' => 'b'],
        ];
        $data     = [
            'skip' => "don't hydrate me",
            'foo'  => [
                'bar' => 'b',
            ],
        ];
        $valueMap = [
            'foo' => [
                'key' => 'bar',
                'map' => [
                    'a' => Asset\BadHydrator::class,
                    'b' => GoodHydrator::class,
                ],
            ],
        ];

        $actual = HydratorUtil::hydrateDiscriminatorValues($data, [], $valueMap);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateDiscriminatorValuesHydratesArray(): void
    {
        $expected = [
            'foo' => [
                (object) ['bar' => 'a'],
                (object) ['bar' => 'b'],
            ],
        ];
        $data     = [
            'foo' => [
                ['bar' => 'a'],
                ['bar' => 'b'],
            ],
        ];
        $valueMap = [
            'foo' => [
                'key' => 'bar',
                'map' => [
                    'a' => GoodHydrator::class,
                    'b' => GoodHydrator::class,
                ],
            ],
        ];

        $actual = HydratorUtil::hydrateDiscriminatorValues($data, ['foo'], $valueMap);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateDiscriminatorListsTypeErrorThrowsException(): void
    {
        $data    = [
            'foo' => ['a' => 1],
        ];
        $listMap = [
            'foo' => [
                TypeErrorHydrator::class => ['a'],
            ],
        ];

        self::expectException(HydrationException::class);
        self::expectExceptionMessage("Bad type");
        HydratorUtil::hydrateDiscriminatorLists($data, [], $listMap);
    }

    public function testHydrateDiscriminatorListsSkipsIgnoresMissingData(): void
    {
        $expected = [
            'skip' => "don't hydrate me",
        ];
        $listMap  = [
            'foo' => [
                Asset\BadHydrator::class => ['b', 'd'],
            ],
        ];

        $actual = HydratorUtil::hydrateDiscriminatorLists($expected, [], $listMap);
        self::assertSame($expected, $actual);
    }

    public function testHydrateDiscriminatorListsHydratesMostMatchedProperties(): void
    {
        $expected = [
            'skip' => "don't hydrate me",
            'foo'  => (object) ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
        ];
        $data     = [
            'skip' => "don't hydrate me",
            'foo'  => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
        ];
        $listMap  = [
            'foo' => [
                GoodHydrator::class      => ['a', 'b', 'd'],
                Asset\BadHydrator::class => ['b', 'd'],
            ],
        ];

        $actual = HydratorUtil::hydrateDiscriminatorLists($data, [], $listMap);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateDiscriminatorListsHydratesArray(): void
    {
        $expected = [
            'foo' => [
                (object) ['a' => 1],
                (object) ['b' => 2],
            ],
        ];
        $data     = [
            'foo' => [
                ['a' => 1],
                ['b' => 2],
            ],
        ];
        $listMap  = [
            'foo' => [
                GoodHydrator::class => ['a', 'b'],
            ],
        ];

        $actual = HydratorUtil::hydrateDiscriminatorLists($data, ['foo'], $listMap);
        self::assertEquals($expected, $actual);
    }

    public function testHydratePropertiesTypeErrorThrowsException(): void
    {
        $data        = [
            'foo' => ['a' => 1],
        ];
        $propertyMap = [
            'foo' => TypeErrorHydrator::class,
        ];

        self::expectException(HydrationException::class);
        self::expectExceptionMessage("Bad type");
        HydratorUtil::hydrateProperties($data, [], $propertyMap);
    }

    public function testHydratePropertiesIgnoresMissingProperty(): void
    {
        $expected    = [
            'skip' => "don't hydrate me",
        ];
        $propertyMap = [
            'foo' => TypeErrorHydrator::class,
        ];

        $actual = HydratorUtil::hydrateProperties($expected, [], $propertyMap);
        self::assertSame($expected, $actual);
    }

    public function testHydrateProperties(): void
    {
        $expected    = [
            'skip' => "don't hydrate me",
            'foo'  => (object) ['a' => 1],
        ];
        $data        = [
            'skip' => "don't hydrate me",
            'foo'  => ['a' => 1],
        ];
        $propertyMap = [
            'foo' => GoodHydrator::class,
        ];

        $actual = HydratorUtil::hydrateProperties($data, [], $propertyMap);
        self::assertEquals($expected, $actual);
    }

    public function testHydratePropertiesHydratesArray(): void
    {
        $expected    = [
            'foo' => [
                (object) ['a' => 1],
                (object) ['b' => 2],
            ],
        ];
        $data        = [
            'foo' => [
                ['a' => 1],
                ['b' => 2],
            ],
        ];
        $propertyMap = [
            'foo' => GoodHydrator::class,
        ];

        $actual = HydratorUtil::hydrateProperties($data, ['foo'], $propertyMap);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateArrayHydratesObject(): void
    {
        $expected = [
            (object) ['a' => 1],
            (object) ['b' => 2],
        ];
        $data     = [
            ['a' => 1],
            ['b' => 2],
        ];

        $actual = HydratorUtil::hydrateArray('foo', $data, GoodHydrator::class);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateArrayHydratesStringObject(): void
    {
        $expected = [
            new DateTimeImmutable('2024-04-04 10:49:53.000000'),
            new DateTimeImmutable('2024-04-04 10:50:53.000000'),
        ];
        $data     = [
            '2024-04-04 10:49:53.000000',
            '2024-04-04 10:50:53.000000',
        ];

        $actual = HydratorUtil::hydrateArray('foo', $data, DateTimeImmutableHydrator::class);
        self::assertEquals($expected, $actual);
    }

    public function testHydrateEnumsValueErrorThrowsException(): void
    {
        $data  = [
            'foo' => 'baz',
        ];
        $enums = [
            'foo' => MockEnum::class,
        ];

        self::expectException(HydrationException::class);
        self::expectExceptionMessage('Cannot hydrate foo: "baz" is not a valid backing value');
        HydratorUtil::hydrateEnums($data, [], $enums);
    }

    public function testHydrateEnumsIgnoresMissingData(): void
    {
        $expected = [
            'skip' => "don't hydrate me",
        ];
        $enums    = [
            'foo' => MockEnum::class,
        ];

        $actual = HydratorUtil::hydrateEnums($expected, [], $enums);
        self::assertSame($expected, $actual);
    }

    public function testHydrateEnums(): void
    {
        $expected = [
            'skip' => "don't hydrate me",
            'foo'  => MockEnum::Foo,
        ];
        $data     = [
            'skip' => "don't hydrate me",
            'foo'  => 'foo',
        ];
        $enums    = [
            'foo' => MockEnum::class,
        ];

        $actual = HydratorUtil::hydrateEnums($data, [], $enums);
        self::assertSame($expected, $actual);
    }

    public function testHydrateEnumsHydratesArray(): void
    {
        $expected = [
            'foo' => [
                MockEnum::Foo,
                MockEnum::Bar,
            ],
        ];
        $data     = [
            'foo' => [
                'foo',
                'bar',
            ],
        ];
        $enums    = [
            'foo' => MockEnum::class,
        ];

        $actual = HydratorUtil::hydrateEnums($data, ['foo'], $enums);
        self::assertSame($expected, $actual);
    }

    public function testGetMappedProperties(): void
    {
        $expected = [
            'b' => 1,
            'd' => [
                'foo' => 'bar',
            ],
        ];
        $map      = [
            'a' => 'b',
            'c' => 'd',
        ];
        $data     = [
            'a'         => $expected['b'],
            'c'         => $expected['d'],
            'filter me' => 'foo',
        ];

        $actual = HydratorUtil::getMappedProperties($data, $map);
        self::assertSame($expected, $actual);
    }

    public function testExtractObjectArrayExtracts(): void
    {
        $expected    = [
            [
                'foo' => 'bar',
            ],
        ];
        $object      = new stdClass();
        $object->foo = 'bar';
        $data        = [$object];
        $extractors  = [
            stdClass::class => GoodHydrator::class,
        ];

        $actual = HydratorUtil::extractObjectArray($data, $extractors);
        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider extractMixedArrayProvider
     */
    public function testExtractMixedArrayExtracts(mixed $data, mixed $expected): void
    {
        $extractors = [
            stdClass::class => GoodHydrator::class,
        ];
        /** @psalm-suppress MixedAssignment */
        $actual = HydratorUtil::extractMixedArray($data, $extractors);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, array>
     */
    public static function extractMixedArrayProvider(): array
    {
        $object      = new stdClass();
        $object->foo = 'bar';
        return [
            'object_array' => [[$object], [['foo' => 'bar']]],
            'string_array' => [['a' => 'b'], ['a' => 'b']],
            'object'       => [$object, ['foo' => 'bar']],
            'scalar'       => ['foo', 'foo'],
        ];
    }

    public function testExtractDataExtractsMethods(): void
    {
        $expected = [
            'one'   => 'foo',
            'three' => true,
        ];
        $object   = new Extractable('foo', 2, true);
        $methods  = ['one' => 'getOne', 'three' => 'isThree'];

        $actual = HydratorUtil::extractData($object, $methods);
        self::assertSame($expected, $actual);
    }

    public function testExtractEnumsExtrasSingleEnum(): void
    {
        $expected = [
            'first' => 'first',
            'enum'  => 'foo',
        ];
        $data     = [
            'first' => 'first',
            'enum'  => MockEnum::Foo,
        ];

        $actual = HydratorUtil::extractEnums($data, [], ['enum' => MockEnum::class]);
        self::assertSame($expected, $actual);
    }

    public function testExtractEnumsExtractsArrayOfEnums(): void
    {
        $expected = [
            'first' => 'first',
            'enum'  => ['foo', 'bar'],
        ];
        $data     = [
            'first' => 'first',
            'enum'  => [MockEnum::Foo, MockEnum::Bar],
        ];

        $actual = HydratorUtil::extractEnums($data, ['enum'], ['enum' => MockEnum::class]);
        self::assertSame($expected, $actual);
    }

    public function testExtractPropertiesExtractsSingleProperty(): void
    {
        $expected    = [
            'first'  => 'first',
            'object' => [
                'foo' => 'bar',
            ],
        ];
        $object      = new stdClass();
        $object->foo = 'bar';
        $data        = [
            'first'  => 'first',
            'object' => $object,
        ];

        $actual = HydratorUtil::extractProperties($data, [], ['object' => GoodHydrator::class]);
        self::assertSame($expected, $actual);
    }

    public function testExtractPropertiesExtractsArrayProperty(): void
    {
        $expected    = [
            'first'  => 'first',
            'object' => [
                ['foo' => 'bar'],
                ['foo' => 'baz'],
            ],
        ];
        $first       = new stdClass();
        $first->foo  = 'bar';
        $second      = new stdClass();
        $second->foo = 'baz';
        $data        = [
            'first'  => 'first',
            'object' => [$first, $second],
        ];

        $actual = HydratorUtil::extractProperties($data, ['object'], ['object' => GoodHydrator::class]);
        self::assertSame($expected, $actual);
    }
}
