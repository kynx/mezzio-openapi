<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Rize\UriTemplate;

use function count;
use function str_replace;

/**
 * @link https://spec.openapis.org/oas/v3.1.0#style-examples
 * @link https://swagger.io/docs/specification/serialization/
 * @see \KynxTest\Mezzio\OpenApi\Request\AbstractOperationParserTest
 */
abstract class AbstractOperationParser
{
    public function __construct(protected readonly UriTemplate $uriTemplate = new UriTemplate())
    {
    }

    abstract public function getOperation(ServerRequestInterface $request): object;

    abstract protected function getPathTemplate(): string;

    abstract protected function getQueryTemplate(): string;

    /**
     * @return array<string, string>
     */
    abstract protected function getHeaderTemplates(): array;

    /**
     * @return array<string, string>
     */
    abstract protected function getCookieTemplates(): array;

    /**
     * @return array<string, string>
     */
    protected function getPathVariables(UriInterface $uri): array
    {
        /** @var array<string, string> $variables */
        $variables = $this->uriTemplate->extract($this->getPathTemplate(), $uri->getPath()) ?? [];
        return $variables;
    }

    /**
     * @return array<string, string>
     */
    protected function getQueryVariables(UriInterface $uri): array
    {
        /** @var array<string, string> $variables */
        $variables = $this->uriTemplate->extract($this->getQueryTemplate(), '?' . $uri->getQuery()) ?? [];
        return $variables;
    }

    /**
     * @return array<string, string|null>
     */
    protected function getCookieVariables(ServerRequestInterface $request): array
    {
        $variables = [];
        /** @var array<string, string> $cookies */
        $cookies = $request->getCookieParams();
        foreach ($this->getCookieTemplates() as $name => $template) {
            $cookieName = $this->normalizeCookieName($name);
            $cookie     = $cookies[$cookieName] ?? '';
            /** @var null|array<string, string|null> $extracted */
            $extracted        = $this->uriTemplate->extract($template, $cookie);
            $variables[$name] = $extracted[$name] ?? null;
        }

        return $variables;
    }

    private function normalizeCookieName(string $name): string
    {
        return str_replace(['.', ' '], '_', $name);
    }

    /**
     * @return array<string, string|null>
     */
    protected function getHeaderVariables(ServerRequestInterface $request): array
    {
        $variables = [];
        foreach ($this->getHeaderTemplates() as $name => $template) {
            $header = $request->getHeaderLine($name);
            /** @var null|array<string, string|null> $extracted */
            $extracted        = $this->uriTemplate->extract($template, $header);
            $variables[$name] = $extracted[$name] ?? null;
        }

        return $variables;
    }

    /**
     * @param list<string> $list
     * @return array<string, string|null>
     */
    protected function listToAssociativeArray(array $list): array
    {
        $assoc = [];
        for ($i = 0; $i < count($list); $i = $i + 2) {
            $assoc[$list[$i]] = $list[$i + 1] ?? null;
        }

        return $assoc;
    }
}
