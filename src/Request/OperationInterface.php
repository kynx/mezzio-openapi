<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Request;

interface OperationInterface
{
    public function getPathParams(): object|null;

    public function getQueryParams(): object|null;

    public function getHeaderParams(): object|null;

    public function getCookieParams(): object|null;

    public function getRequestBody(): object|null;
}
