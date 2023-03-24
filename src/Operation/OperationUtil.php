<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;
use Rize\UriTemplate;

use function count;
use function is_array;
use function str_replace;
use function urldecode;

/**
 * @link https://spec.openapis.org/oas/v3.1.0#style-examples
 * @link https://swagger.io/docs/specification/serialization/
 */
final class OperationUtil
{
    /**
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
        return self::urlDecode($variables);
    }

    /**
     * @return array<string, array|string|null>
     */
    public static function getQueryVariables(
        UriTemplate $uriTemplate,
        string $template,
        ServerRequestInterface $request
    ): array {
        /** @var array<string, array|string|null> $variables */
        $variables = $uriTemplate->extract($template, '?' . $request->getUri()->getQuery()) ?? [];
        return self::urlDecode($variables);
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

        return self::urlDecode($variables);
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

    /**
     * @param array<array-key, string|null>|string|null $value
     */
    public static function castToScalar(array|string|null $value, string $type): bool|float|int|string|null
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

    public static function castToScalarArray(array $values, string $type): array
    {
        return array_map(
            fn (string|null $value): bool|float|int|string|null => self::castToScalar($value, $type),
            $values
        );
    }

    /**
     * Converts unexploded object to an associative array.
     *
     * For example: `R,100,G,200,B,150` -> `["R" => 100, "G" => 200, "B" => 150]`. The OpenAPI spec is crazy for
     * permitting this kind of stuff. Stay sane: use the exploded form instead.
     *
     * @see https://spec.openapis.org/oas/v3.1.0#style-examples
     *
     * @param array<int, string>|null $list
     * @return array<string, string|null>
     */
    public static function listToAssociativeArray(array|null $list): array
    {
        if ($list === null) {
            return [];
        }

        $assoc = [];
        for ($i = 0; $i < count($list); $i = $i + 2) {
            $assoc[$list[$i]] = $list[$i + 1] ?? null;
        }

        return $assoc;
    }

    /**
     * @template TKey
     * @template TValue
     * @param array<TKey, TValue> $variables
     * @return array<TKey, TValue>
     */
    private static function urlDecode(array $variables): array
    {
        foreach ($variables as $i => $variable) {
            if (is_array($variable)) {
                $variables[$i] = self::urlDecode($variable);
            } elseif ($variable !== null) {
                $variables[$i] = urldecode($variable);
            }
        }

        return $variables;
    }
}
