<?php

declare(strict_types=1);

namespace App\Oauth;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class PostTokenAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/oauth/token', self::class);
    }

    public function __construct(private AuthorizationServer $authServer)
    {
    }

    /** @throws OAuthServerException */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return $this->authServer->respondToAccessTokenRequest(
            $request,
            $response,
        );
    }
}
