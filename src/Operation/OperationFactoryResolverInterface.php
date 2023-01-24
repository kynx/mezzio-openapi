<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;

interface OperationFactoryResolverInterface
{
    public function getFactory(ServerRequestInterface $request): OperationFactoryInterface|null;
}
