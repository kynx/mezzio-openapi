<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Middleware\Exception\InvalidOperationException;
use Kynx\Mezzio\OpenApi\RouteOptionsUtil;
use Psr\Http\Message\ServerRequestInterface;

final class MezzioRequestFactoryResolver implements RequestFactoryResolverInterface
{
    /**
     * @param array<string, class-string> $operationFactories
     */
    public function __construct(private readonly array $operationFactories)
    {
    }

    public function getFactory(ServerRequestInterface $request): RequestFactoryInterface|null
    {
        $jsonPointer = RouteOptionsUtil::getJsonPointer($request);
        if ($jsonPointer === null) {
            throw InvalidOperationException::missingPointer($request->getUri()->getPath());
        }

        /** @var class-string<RequestFactoryInterface>|null $factoryClass */
        $factoryClass = $this->operationFactories[$jsonPointer] ?? null;
        if ($factoryClass === null) {
            throw InvalidOperationException::missingRequestFactory($jsonPointer);
        }

        return new $factoryClass();
    }
}
