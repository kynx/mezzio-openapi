<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Hydrator;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorException;
use Kynx\Mezzio\OpenApi\Hydrator\HydratorUtil;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\GoodHydrator;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\MockEnum;
use KynxTest\Mezzio\OpenApi\Hydrator\Asset\TypeErrorHydrator;
use PHPUnit\Framework\TestCase;

/**
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

        self::expectException(HydratorException::class);
        self::expectExceptionMessage("Property 'foo' is missing discriminator property 'baz'");
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

        self::expectException(HydratorException::class);
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

        self::expectException(HydratorException::class);
        self::expectExceptionMessage("Bad type");
        HydratorUtil::hydrateDiscriminatorValues($data, [], $valueMap);
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

    public function testHydrateDiscriminatorListTypeErrorThrowsException(): void
    {
        $data    = [
            'foo' => ['a' => 1],
        ];
        $listMap = [
            'foo' => [
                TypeErrorHydrator::class => ['a'],
            ],
        ];

        self::expectException(HydratorException::class);
        self::expectExceptionMessage("Bad type");
        HydratorUtil::hydrateDiscriminatorLists($data, [], $listMap);
    }

    public function testHydrateDiscriminatorListHydratesMostMatchedProperties(): void
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

    public function testHydrateDiscriminatorListHydratesArray(): void
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

        self::expectException(HydratorException::class);
        self::expectExceptionMessage("Bad type");
        HydratorUtil::hydrateProperties($data, [], $propertyMap);
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

    public function testHydrateEnumsValueErrorThrowsException(): void
    {
        $data  = [
            'foo' => 'baz',
        ];
        $enums = [
            'foo' => MockEnum::class,
        ];

        self::expectException(HydratorException::class);
        self::expectExceptionMessage('Cannot hydrate foo: "baz" is not a valid backing value');
        HydratorUtil::hydrateEnums($data, [], $enums);
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
}
