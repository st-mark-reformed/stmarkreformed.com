<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class SMRCAuthHook implements CustomAuthenticationHook
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function process(
        OauthUserInfo $userInfo,
        ServerRequestInterface $request,
        ResponseInterface $defaultAccessDeniedResponse,
    ): CustomAuthenticationResult {
        $user = $this->repository->findByEmail($userInfo->email);

        if ($user === null || ! $user->isActive) {
            return new CustomAuthenticationResult(
                response: $defaultAccessDeniedResponse,
            );
        }

        return new CustomAuthenticationResult(
            request: $request->withAttribute('user', $user),
        );
    }
}
