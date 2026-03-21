<?php

declare(strict_types=1);

namespace App;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class ResourceServerMiddleware implements MiddlewareInterface
{
    public function __construct(private ResourceServer $server)
    {
    }

    /** @throws OAuthServerException */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $request = $this->server->validateAuthenticatedRequest(
            $request,
        );

        return $handler->handle($request);
    }
}
