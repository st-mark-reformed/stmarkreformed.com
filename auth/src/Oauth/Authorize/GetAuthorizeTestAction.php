<?php

declare(strict_types=1);

namespace App\Oauth\Authorize;

use App\User\UserRepository;
use Config\RuntimeConfigOptions;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

readonly class GetAuthorizeTestAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $runtimeConfig = new RuntimeConfig();

        $enableTestRoutes = $runtimeConfig->getBoolean(
            RuntimeConfigOptions::ENABLE_TEST_ROUTES,
        );

        if (! $enableTestRoutes) {
            return;
        }

        $routes->get('/oauth/authorize/test', self::class);
    }

    public function __construct(
        private AuthorizationServer $authServer,
        private UserRepository $userRepository,
    ) {
    }

    /** @throws OAuthServerException */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $authRequest = $this->authServer->validateAuthorizationRequest(
            $request,
        );

        $user = $this->userRepository->findByEmail('tj@buzzingpixel.com');

        $authRequest->setUser(new UserEntityForLeagueOauth(
            /** @phpstan-ignore-next-line */
            $user->email->toString(),
        ));

        $authRequest->setAuthorizationApproved(true);

        return $this->authServer->completeAuthorizationRequest(
            $authRequest,
            $response,
        );
    }
}
