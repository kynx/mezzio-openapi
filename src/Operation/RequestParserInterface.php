<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;

interface RequestParserInterface
{
    public function parse(ServerRequestInterface $request): object;
}