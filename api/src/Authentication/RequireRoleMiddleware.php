<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\User\User\Role;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function assert;
use function implode;
use function json_encode;

readonly abstract class RequireRoleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    protected function processInternal(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        Role $role,
    ): ResponseInterface {
        $userInfo = $request->getAttribute('oauthUserInfo');
        assert($userInfo instanceof OauthUserInfo);

        if (! $userInfo->hasRole($role->humanReadable())) {
            return $this->sendAccessDenied($role);
        }

        return $handler->handle($request);
    }

    private function sendAccessDenied(Role $role): ResponseInterface
    {
        $msg = implode(' ', [
            'You must have the role',
            '"' . $role->humanReadable() . '"',
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
