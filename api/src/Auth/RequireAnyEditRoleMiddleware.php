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

use function array_intersect;
use function array_map;
use function count;
use function json_encode;

/**
 * Gates the shared upload endpoints: passes if the user holds any content-edit
 * role. Staging bytes is harmless on its own — the consequential authorization
 * (which feature the upload is attached to) is enforced by that feature's own
 * create/edit route when the upload is claimed.
 *
 * Like RequireRoleMiddleware, this also runs RequireOauthTokenHeaderMiddleware
 * when needed, so it does not need to be on the middleware stack separately.
 */
class RequireAnyEditRoleMiddleware implements
    MiddlewareInterface,
    RequestHandlerInterface
{
    private const array EDIT_ROLES = [
        UserRole::EDIT_MESSAGES,
        UserRole::EDIT_PROFILES,
        UserRole::EDIT_NEWS,
        UserRole::EDIT_MEN_OF_THE_MARK,
        UserRole::EDIT_PASTORS_PAGE,
        UserRole::EDIT_HYMNS_OF_THE_MONTH,
        UserRole::EDIT_RESOURCES,
        UserRole::EDIT_MAILING_LISTS,
    ];

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RequireOauthTokenHeaderMiddleware $requireOauthToken,
    ) {
    }

    private RequestHandlerInterface $currentHandler;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $this->currentHandler = $handler;

        $userinfo = $request->getAttribute('oauthUserInfo');

        if ($userinfo instanceof OauthUserInfo) {
            return $this->handle($request);
        }

        return $this->requireOauthToken->process($request, $this);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userinfo = $request->getAttribute('oauthUserInfo');

        if (! $userinfo instanceof OauthUserInfo) {
            return $this->sendAccessDenied();
        }

        $allowedRoles = array_map(
            static fn (UserRole $role): string => $role->name,
            self::EDIT_ROLES,
        );

        if (count(array_intersect($allowedRoles, $userinfo->roles)) === 0) {
            return $this->sendAccessDenied();
        }

        return $this->currentHandler->handle($request);
    }

    private function sendAccessDenied(): ResponseInterface
    {
        $msg = 'You must have a content-editing role to access this area';

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
