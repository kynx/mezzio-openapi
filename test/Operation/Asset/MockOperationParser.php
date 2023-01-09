<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Asset;

use Kynx\Mezzio\OpenApi\Operation\AbstractOperationParser;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockOperation;
use Psr\Http\Message\ServerRequestInterface;
use Rize\UriTemplate;

final class MockOperationParser extends AbstractOperationParser
{
    /**
     * @param array<string, string> $headerTemplates
     * @param array<string, string> $cookieTemplates
     */
    public function __construct(
        private readonly string $pathTemplate,
        private readonly string $queryTemplate,
        private readonly array $headerTemplates,
        private readonly array $cookieTemplates,
        protected readonly UriTemplate $uriTemplate = new UriTemplate()
    ) {
    }

    public function getOperation(ServerRequestInterface $request): MockOperation
    {
        $uri = $request->getUri();
        return new MockOperation(
            $this->getPathVariables($uri),
            $this->getQueryVariables($uri),
            $this->getHeaderVariables($request),
            $this->getCookieVariables($request)
        );
    }

    protected function getPathTemplate(): string
    {
        return $this->pathTemplate;
    }

    protected function getQueryTemplate(): string
    {
        return $this->queryTemplate;
    }

    protected function getHeaderTemplates(): array
    {
        return $this->headerTemplates;
    }

    protected function getCookieTemplates(): array
    {
        return $this->cookieTemplates;
    }

    public function listToAssociativeArray(array $list): array
    {
        return parent::listToAssociativeArray($list);
    }
}
