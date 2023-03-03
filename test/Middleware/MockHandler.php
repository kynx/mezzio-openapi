<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MockHandler implements RequestHandlerInterface
{
    private ServerRequestInterface $handledRequest;

    public function __construct(private ResponseInterface $response = new EmptyResponse())
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->handledRequest = $request;
        return $this->response;
    }

    public function getHandledRequest(): ServerRequestInterface
    {
        return $this->handledRequest;
    }
}
