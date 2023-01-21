<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ResponseFactoryInterface
{
    public function negotiate(ServerRequestInterface $request): string;

    public function getResponse(string $status, string $mimeType, object|null $responseBody = null): ResponseInterface;
}
