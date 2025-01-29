<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\Middleware\OperationAddressResolverInterface;
use Kynx\Mezzio\OpenApi\RouteOptionsUtil;
use League\OpenAPIValidation\PSR7\OperationAddress;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_pop;
use function array_slice;
use function explode;
use function implode;
use function str_replace;

final class MezzioOperationAddressResolver implements OperationAddressResolverInterface
{
    public function getOperationAddress(ServerRequestInterface $request): OperationAddress
    {
        $jsonPointer = RouteOptionsUtil::getJsonPointer($request);
        if ($jsonPointer === null) {
            throw InvalidOperationException::missingPointer($request->getUri()->getPath());
        }

        // '/paths/~1pet~1{petId}/get' => ['paths', 'pet', '{petId}', 'get']
        $parts = array_filter(explode('/', str_replace(['~0', '~1'], ['~', '/'], $jsonPointer)));
        return new OperationAddress(
            '/' . implode('/', array_slice($parts, 1, -1)),
            (string) array_pop($parts)
        );
    }
}
