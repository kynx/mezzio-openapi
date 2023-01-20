<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_keys;
use function array_slice;
use function array_values;
use function count;
use function explode;
use function preg_split;
use function trim;
use function uasort;

/**
 * @link https://spec.openapis.org/oas/v3.1.0#fixed-fields-10
 * @link https://datatracker.ietf.org/doc/html/rfc6838#section-4.2.8
 */
final class ContentTypeNegotiator
{
    private string|null $default;
    /** @var array<string, list<string>> */
    private array $matchers;

    /**
     * @param non-empty-list<string> $mimeTypes
     */
    public function __construct(array $mimeTypes)
    {
        $this->default  = null;
        $this->matchers = [];

        foreach ($mimeTypes as $mimeType) {
            if ($mimeType === '*/*') {
                $this->default = $mimeType;
                continue;
            }

            $this->matchers[$mimeType] = array_values(
                array_filter(
                    preg_split('#[/+]#', $mimeType),
                    fn(string $part) => $part !== '*'
                )
            );
        }

        uasort($this->matchers, fn (array $a, array $b) => count($b) <=> count($a));
    }

    /**
     * Returns matched mime type based on Content-Type header
     */
    public function negotiate(ServerRequestInterface $request): string
    {
        [$type]   = explode(';', $request->getHeaderLine('Content-Type'));
        $mimeType = $this->getMatched(preg_split('#[/+]#', trim($type)));
        if ($mimeType === null) {
            throw OperationException::invalidContentType($type, $this->getMimeTypes());
        }

        return $mimeType;
    }

    private function getMatched(array $parts): string|null
    {
        $suffixed = count($parts) > 2 ? [$parts[0], $parts[2]] : [];
        foreach ($this->matchers as $mimeType => $matcher) {
            if (array_slice($parts, 0, count($matcher)) === $matcher) {
                return $mimeType;
            }
            // match application/geo+json header to application/json
            if ($suffixed !== [] && array_slice($suffixed, 0, count($matcher)) === $matcher) {
                return $mimeType;
            }
        }

        return $this->default;
    }

    /**
     * @return list<string>
     */
    private function getMimeTypes(): array
    {
        return array_keys($this->matchers);
    }
}
