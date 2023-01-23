<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Operation\OperationFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

interface OperationFactoryResolverInterface
{
    public function getFactory(ServerRequestInterface $request): OperationFactoryInterface;
}
