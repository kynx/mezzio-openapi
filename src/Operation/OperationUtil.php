<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;
use Rize\UriTemplate;

use function array_key_exists;
use function array_map;
use function count;
use function current;
use function is_array;
use function is_string;
use function str_replace;
use function urldecode;

/**
 * @link https://spec.openapis.org/oas/v3.1.0#style-examples
 * @link https://swagger.io/docs/specification/serialization/
 */
final class OperationUtil
{
    /**
     * @psalm-suppress UnusedConstructor
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getPathVariables(
        UriTemplate $uriTemplate,
        string $template,
        ServerRequestInterface $request
    ): array {
        /** @var array<string, string> $variables */
        $variables = $uriTemplate->extract($template, $request->getUri()->getPath()) ?? [];
        /** @var array<string, string> $decoded */
        $decoded = self::urlDecode($variables);
        return $decoded;
    }

    /**
     * @return array<string, array|string>
     */
    public static function getQueryVariables(
        UriTemplate $uriTemplate,
        string $template,
        ServerRequestInterface $request
    ): array {
        /** @var array<string, array<string, string|null>|string|null> $variables */
        $variables = $uriTemplate->extract($template, '?' . $request->getUri()->getQuery()) ?? [];
        /** @var array<string, array|string> $decoded */
        $decoded = self::urlDecode($variables);
        return $decoded;
    }

    /**
     * @param array<string, string> $templates
     * @return array<string, string|null>
     */
    public static function getCookieVariables(
        UriTemplate $uriTemplate,
        array $templates,
        ServerRequestInterface $request
    ): array {
        $variables = [];
        /** @var array<string, string> $cookies */
        $cookies = $request->getCookieParams();
        foreach ($templates as $name => $template) {
            $cookieName = self::normalizeCookieName($name);
            $cookie     = $cookies[$cookieName] ?? '';
            /** @var null|array<string, string|null> $extracted */
            $extracted        = $uriTemplate->extract($template, $cookie);
            $variables[$name] = $extracted[$name] ?? null;
        }

        /** @var array<string, string> $decoded */
        $decoded = self::urlDecode($variables);
        return $decoded;
    }

    private static function normalizeCookieName(string $name): string
    {
        return str_replace(['.', ' '], '_', $name);
    }

    /**
     * @param array<string, string> $templates
     * @return array<string, string|null>
     */
    public static function getHeaderVariables(
        UriTemplate $uriTemplate,
        array $templates,
        ServerRequestInterface $request
    ): array {
        $variables = [];
        foreach ($templates as $name => $template) {
            $header = $request->getHeaderLine($name);
            /** @var null|array<string, string|null> $extracted */
            $extracted        = $uriTemplate->extract($template, $header);
            $variables[$name] = $extracted[$name] ?? null;
        }

        return $variables;
    }

    public static function castToScalar(array $data, string $key, string $type): array
    {
        if (! array_key_exists($key, $data)) {
            return $data;
        }

        /** @var array<array-key, string|null>|string|null $value */
        $value      = $data[$key];
        $data[$key] = self::castToScalarValue($value, $type);
        return $data;
    }

    /**
     * @param array<array-key, string|null>|string|null $value
     */
    private static function castToScalarValue(array|string|null $value, string $type): bool|float|int|string|null
    {
        if (is_array($value)) {
            $value = current($value);
        }
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'bool'  => (bool) $value,
            'float' => (float) $value,
            'int'   => (int) $value,
            default => (string) $value
        };
    }

    public static function castToScalarArray(array $data, string $key, string $type): array
    {
        if (! (array_key_exists($key, $data) && is_array($data[$key]))) {
            return $data;
        }
        $data[$key] = array_map(
            fn (string|null $value): bool|float|int|string|null => self::castToScalarValue($value, $type),
            $data[$key]
        );

        return $data;
    }

    /**
     * Converts unexploded object to an associative array.
     *
     * For example: `R,100,G,200,B,150` -> `["R" => 100, "G" => 200, "B" => 150]`. The OpenAPI spec is crazy for
     * permitting this kind of stuff. Stay sane: use the exploded form instead.
     *
     * @see https://spec.openapis.org/oas/v3.1.0#style-examples
     */
    public static function listToAssociativeArray(array $data, string $key): array
    {
        if (! array_key_exists($key, $data)) {
            return $data;
        }
        $value = $data[$key];
        if (! is_array($value)) {
            return [];
        }

        $assoc = [];
        for ($i = 0; $i < count($value); $i += 2) {
            /** @var string $k */
            $k = $value[$i];
            /** @var scalar|null $v */
            $v         = $value[$i + 1] ?? null;
            $assoc[$k] = $v;
        }

        $data[$key] = $assoc;
        return $data;
    }

    /**
     * @param array<string, array<string, string|null>|string|null> $variables
     */
    private static function urlDecode(array $variables): array
    {
        foreach ($variables as $i => $variable) {
            if (is_array($variable)) {
                $variables[$i] = self::urlDecode($variable);
            } elseif (is_string($variable)) {
                $variables[$i] = urldecode($variable);
            }
        }

        return $variables;
    }
}
