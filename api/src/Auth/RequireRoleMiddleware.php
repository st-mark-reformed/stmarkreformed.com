<?php

declare(strict_types=1);

namespace App\Auth;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function array_find;
use function implode;
use function json_encode;

/**
 * This middleware is designed to also run the RequireOauthTokenHeaderMiddleware
 * if needed, so you don't need to have it on the middleware stack also
 */
abstract class RequireRoleMiddleware implements
    MiddlewareInterface,
    RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RequireOauthTokenHeaderMiddleware $requireOauthToken,
    ) {
    }

    private RequestHandlerInterface $currentHandler;

    abstract protected function getRole(): UserRole;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $this->currentHandler = $handler;

        /**
         * If the userInfo attribute has already been set, the
         * RequireOauthTokenHeaderMiddleware has already run so we don't
         * need to run it again.
         */
        $userinfo = $request->getAttribute('oauthUserInfo');
        if ($userinfo instanceof OauthUserInfo) {
            return $this->handle($request);
        }

        return $this->requireOauthToken->process(
            $request,
            $this,
        );
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userinfo = $request->getAttribute('oauthUserInfo');

        if (! $userinfo instanceof OauthUserInfo) {
            return $this->sendAccessDenied();
        }

        $hasRole = array_find(
            $userinfo->roles,
            function (string $role): bool {
                return $role === $this->getRole()->name;
            },
        );

        if ($hasRole === null) {
            return $this->sendAccessDenied();
        }

        return $this->currentHandler->handle($request);
    }

    private function sendAccessDenied(): ResponseInterface
    {
        $msg = implode(' ', [
            'You must have the role',
            '"' . $this->getRole()->name . '"',
            'to access this area',
        ]);

        $response = $this->responseFactory->createResponse();

        $response = $response->withHeader(
            'Content-type',
            'application/json',
        );

        $response->getBody()->write((string) json_encode([
            'error' => 'access_denied',
            'error_description' => $msg,
            'message' => $msg,
        ]));

        return $response->withStatus(403);
    }
}
