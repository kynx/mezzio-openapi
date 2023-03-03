<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use League\OpenAPIValidation\PSR7\OperationAddress;
use Psr\Http\Message\ServerRequestInterface;

interface OperationAddressResolverInterface
{
    /**
     * Returns operation address object from request
     *
     * @throws InvalidOperationException
     */
    public function getOperationAddress(ServerRequestInterface $request): OperationAddress;
}
