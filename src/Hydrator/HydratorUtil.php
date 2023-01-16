<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

use BackedEnum;
use TypeError;
use ValueError;

use function array_intersect;
use function array_key_last;
use function array_keys;
use function array_map;
use function asort;
use function assert;
use function count;
use function in_array;
use function is_array;
use function is_string;

/**
 * @see \KynxTest\Mezzio\OpenApi\Hydrator\HydratorUtilTest
 *
 * phpcs:ignore Generic.Files.LineLength.TooLong
 * @psalm-type DiscriminatorValue = array{key: string, map: array<string, class-string<HydratorInterface>}
 * @psalm-type DiscriminatorValueArray = array<string, DiscriminatorValue>
 * @psalm-type DiscriminatorList = array<class-string<HydratorInterface>, list<string>>
 * @psalm-type DiscriminatorListArray = array<string, DiscriminatorList>
 * @psalm-immutable
 */
final class HydratorUtil
{
    private function __construct()
    {
    }

    /**
     * Returns hydrated object based on the descriminator property value
     *
     * @link https://spec.openapis.org/oas/v3.1.0#discriminator-object
     *
     * @param DiscriminatorValueArray $valueMap
     */
    public static function hydrateDiscriminatorValues(array $data, array $arrayProperties, array $valueMap): array
    {
        foreach ($valueMap as $property => $discriminator) {
            if (! isset($data[$property])) {
                continue;
            }

            $propertyData = $data[$property];
            assert(is_array($propertyData));

            if (in_array($property, $arrayProperties, true)) {
                $hydrated = array_map(
                    fn (array $value): object => self::hydrateDiscriminatorValue($property, $value, $discriminator),
                    $propertyData
                );
            } else {
                $hydrated = self::hydrateDiscriminatorValue($property, $propertyData, $discriminator);
            }

            $data[$property] = $hydrated;
        }

        return $data;
    }

    /**
     * @param array{key: string, map: array<string, class-string<HydratorInterface>>} $discriminator
     */
    private static function hydrateDiscriminatorValue(string $name, array $data, array $discriminator): object
    {
        $key = $discriminator['key'];
        if (! isset($data[$key])) {
            throw HydratorException::missingDiscriminatorProperty($name, $key);
        }

        $value    = (string) $data[$key];
        $hydrator = $discriminator['map'][$value] ?? null;
        if ($hydrator === null) {
            throw HydratorException::invalidDiscriminatorValue($key, $value);
        }

        try {
            return $hydrator::hydrate($data);
        } catch (TypeError $exception) {
            throw HydratorException::fromThrowable($name, $exception);
        }
    }

    /**
     * Returns hydrated object based on number of properties that match those in list
     *
     * This is used with `oneOf` schemas. We assume that your data is structured so each of the choices has different
     * required properties. If there is any ambiguity the hydrator may throw an exception, or the wrong type be
     * hydrated. Use a `discriminator` to avoid this.
     *
     * @link https://spec.openapis.org/oas/v3.1.0#discriminator-object
     *
     * @param DiscriminatorListArray $listMap
     */
    public static function hydrateDiscriminatorLists(array $data, array $arrayProperties, array $listMap): array
    {
        foreach ($listMap as $property => $discriminator) {
            if (! isset($data[$property])) {
                continue;
            }

            $propertyData = $data[$property];
            assert(is_array($propertyData));

            if (in_array($property, $arrayProperties, true)) {
                $hydrated = array_map(
                    fn (array $value): object => self::hydrateDiscriminatorList($property, $value, $discriminator),
                    $propertyData
                );
            } else {
                $hydrated = self::hydrateDiscriminatorList($property, $propertyData, $discriminator);
            }

            $data[$property] = $hydrated;
        }

        return $data;
    }

    /**
     * @param DiscriminatorList $discriminator
     */
    private static function hydrateDiscriminatorList(string $name, array $data, array $discriminator): object
    {
        $matches = array_map(
            fn (array $keys): int => self::countMatchedKeys($data, $keys),
            $discriminator
        );
        asort($matches);

        /** @var class-string<HydratorInterface> $hydrator */
        $hydrator = array_key_last($matches);
        try {
            return $hydrator::hydrate($data);
        } catch (TypeError $exception) {
            throw HydratorException::fromThrowable($name, $exception);
        }
    }

    /**
     * @param list<string> $properties
     */
    private static function countMatchedKeys(array $data, array $properties): int
    {
        return count(array_intersect(array_keys($data), $properties));
    }

    /**
     * @param array<string, class-string<HydratorInterface>> $hydrators
     */
    public static function hydrateProperties(array $data, array $arrayProperties, array $hydrators): array
    {
        foreach ($hydrators as $property => $hydrator) {
            if (! isset($data[$property])) {
                continue;
            }

            $propertyData = $data[$property];
            assert(is_array($propertyData));

            if (in_array($property, $arrayProperties, true)) {
                $hydrated = array_map(
                    fn (array $value): object => self::hydrateProperty($property, $value, $hydrator),
                    $propertyData
                );
            } else {
                $hydrated = self::hydrateProperty($property, $propertyData, $hydrator);
            }

            $data[$property] = $hydrated;
        }

        return $data;
    }

    /**
     * @param class-string<HydratorInterface> $hydrator
     */
    private static function hydrateProperty(string $name, array $data, $hydrator): object
    {
        try {
            return $hydrator::hydrate($data);
        } catch (TypeError $exception) {
            throw HydratorException::fromThrowable($name, $exception);
        }
    }

    /**
     * @param array<string, class-string<BackedEnum>> $enums
     */
    public static function hydrateEnums(array $data, array $arrayProperties, array $enums): array
    {
        foreach ($enums as $property => $enum) {
            if (! isset($data[$property])) {
                continue;
            }

            $propertyData = $data[$property];

            if (in_array($property, $arrayProperties, true)) {
                assert(is_array($propertyData));
                $hydrated = array_map(
                    fn (string $value): BackedEnum => self::hydrateEnum($property, $value, $enum),
                    $propertyData
                );
            } else {
                assert(is_string($propertyData));
                $hydrated = self::hydrateEnum($property, $propertyData, $enum);
            }

            $data[$property] = $hydrated;
        }

        return $data;
    }

    /**
     * @param class-string<BackedEnum> $enum
     */
    private static function hydrateEnum(string $name, string $value, string $enum): BackedEnum
    {
        try {
            return $enum::from($value);
        } catch (ValueError $exception) {
            throw HydratorException::fromThrowable($name, $exception);
        }
    }

    /**
     * @psalm-template TProperty of mixed
     * @param array<string, TProperty> $data
     * @param array<string, string> $map
     * @return array<string, TProperty>
     */
    public static function getMappedProperties(array $data, array $map): array
    {
        $mapped = [];
        foreach ($map as $old => $new) {
            if (isset($data[$old])) {
                $mapped[$new] = $data[$old];
            }
        }

        return $mapped;
    }
}
