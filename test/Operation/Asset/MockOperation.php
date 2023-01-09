<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Asset;

final class MockOperation
{
    public function __construct(
        private readonly array $pathVariables,
        private readonly array $queryVariables,
        private readonly array $headerVariables,
        private readonly array $cookieVariables
    ) {
    }

    public function getPathParams(): object|null
    {
        return (object) $this->pathVariables;
    }

    public function getQueryParams(): object|null
    {
        return (object) $this->queryVariables;
    }

    public function getHeaderParams(): object|null
    {
        return (object) $this->headerVariables;
    }

    public function getCookieParams(): object|null
    {
        return (object) $this->cookieVariables;
    }

    public function getRequestBody(): object|null
    {
        return null;
    }
}
