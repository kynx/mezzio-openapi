<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator;

use BackedEnum;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\HydrationException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\InvalidDiscriminatorException;
use Kynx\Mezzio\OpenApi\Hydrator\Exception\MissingDiscriminatorException;
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
use function is_object;
use function is_string;
use function method_exists;

/**
 * @psalm-type DiscriminatorValue = array{key: string, map: array<string, class-string<HydratorInterface>}
 * @psalm-type DiscriminatorValueArray = array<string, DiscriminatorValue>
 * @psalm-type DiscriminatorList = array<class-string<HydratorInterface>, list<string>>
 * @psalm-type DiscriminatorListArray = array<string, DiscriminatorList>
 * @psalm-immutable
 */
final class HydratorUtil
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Returns hydrated object based on the discriminator property value
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
     * @param DiscriminatorValue $discriminator
     */
    public static function hydrateDiscriminatorValue(string $name, array $data, array $discriminator): object
    {
        $key = $discriminator['key'];
        if (! isset($data[$key])) {
            throw MissingDiscriminatorException::fromMissing($name, $key);
        }

        $value    = (string) $data[$key];
        $hydrator = $discriminator['map'][$value] ?? null;
        if ($hydrator === null) {
            throw InvalidDiscriminatorException::fromValue($key, $value);
        }

        try {
            return $hydrator::hydrate($data);
        } catch (TypeError $exception) {
            throw HydrationException::fromThrowable($name, $exception);
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
    public static function hydrateDiscriminatorList(string $name, array $data, array $discriminator): object
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
            throw HydrationException::fromThrowable($name, $exception);
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
                $hydrated = self::hydrateArray($property, $propertyData, $hydrator);
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
    public static function hydrateArray(string $name, array $data, string $hydrator): array
    {
        return array_map(
            fn (array $value): object => self::hydrateProperty($name, $value, $hydrator),
            $data
        );
    }

    /**
     * @param class-string<HydratorInterface> $hydrator
     */
    private static function hydrateProperty(string $name, array $data, string $hydrator): object
    {
        try {
            return $hydrator::hydrate($data);
        } catch (TypeError $exception) {
            throw HydrationException::fromThrowable($name, $exception);
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
            throw HydrationException::fromThrowable($name, $exception);
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

    /**
     * @param array<string, string> $methods
     * @return array<string, mixed>
     */
    public static function extractData(object $object, array $methods): array
    {
        $data = [];
        foreach ($methods as $key => $method) {
            assert(method_exists($object, $method));
            /** @psalm-suppress MixedAssignment */
            $data[$key] = $object->$method();
        }

        return $data;
    }

    /**
     * @param array<string, class-string<BackedEnum>> $enums
     */
    public static function extractEnums(array $data, array $arrayProperties, array $enums): array
    {
        foreach (array_keys($enums) as $property) {
            $value = $data[$property];
            if (in_array($property, $arrayProperties, true)) {
                assert(is_array($value));
                $enumValues = [];
                /** @psalm-suppress MixedAssignment */
                foreach ($value as $enumValue) {
                    assert($enumValue instanceof BackedEnum);
                    $enumValues[] = $enumValue->value;
                }
                $data[$property] = $enumValues;
            } else {
                assert($value instanceof BackedEnum);
                $data[$property] = $value->value;
            }
        }

        return $data;
    }

    /**
     * @param array<class-string, class-string<HydratorInterface>> $extractors
     */
    public static function extractProperties(array $data, array $arrayProperties, array $extractors): array
    {
        foreach ($data as $property => $value) {
            if (in_array($property, $arrayProperties, true)) {
                assert(is_array($value));
                $data[$property] = self::extractArray($value, $extractors);
            } else {
                /** @psalm-suppress MixedAssignment */
                $data[$property] = self::extractProperty($value, $extractors);
            }
        }

        return $data;
    }

    /**
     * @param array<class-string, class-string<HydratorInterface>> $extractors
     */
    private static function extractArray(array $data, array $extractors): array
    {
        return array_map(fn (object $object): mixed => self::extractProperty($object, $extractors), $data);
    }

    /**
     * @param array<class-string, class-string<HydratorInterface>> $extractors
     */
    private static function extractProperty(mixed $value, array $extractors): mixed
    {
        if (! is_object($value)) {
            return $value;
        }

        assert(isset($extractors[$value::class]));
        return $extractors[$value::class]::extract($value);
    }
}
