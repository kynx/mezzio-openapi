<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;
use Rize\UriTemplate;

use function count;
use function str_replace;

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
        return $variables;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getQueryVariables(
        UriTemplate $uriTemplate,
        string $template,
        ServerRequestInterface $request
    ): array {
        /** @var array<string, string> $variables */
        $variables = $uriTemplate->extract($template, '?' . $request->getUri()->getQuery()) ?? [];
        return $variables;
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

        return $variables;
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
     * @param list<string> $list
     * @return array<string, string|null>
     */
    public static function listToAssociativeArray(array $list): array
    {
        $assoc = [];
        for ($i = 0; $i < count($list); $i = $i + 2) {
            $assoc[$list[$i]] = $list[$i + 1] ?? null;
        }

        return $assoc;
    }
}
