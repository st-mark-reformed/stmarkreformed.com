<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\User\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

use function json_encode;

readonly class SMRCAuthHook implements CustomAuthenticationHook
{
    public function __construct(
        private UserRepository $repository,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function process(
        OauthUserInfo $userInfo,
        ServerRequestInterface $request,
        ResponseInterface $defaultAccessDeniedResponse,
    ): CustomAuthenticationResult {
        $user = $this->repository->findByEmail($userInfo->email);

        if ($user === null || ! $user->isActive) {
            $msg = 'You are not authorized to access this area';

            $response = $this->responseFactory->createResponse(403)
                ->withHeader('Content-type', 'application/json');

            $response->getBody()->write((string) json_encode([
                'error' => 'access_denied',
                'error_description' => $msg,
                'message' => $msg,
            ]));

            return new CustomAuthenticationResult(
                response: $response,
            );
        }

        return new CustomAuthenticationResult(
            request: $request->withAttribute('user', $user),
        );
    }
}
