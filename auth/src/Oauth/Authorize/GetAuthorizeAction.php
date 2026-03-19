<?php

declare(strict_types=1);

namespace App\Oauth\Authorize;

use App\LogIn\GetLogInActionHandler;
use App\User\UserSessionRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetAuthorizeAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/oauth/authorize', self::class);
    }

    public function __construct(
        private AuthorizationServer $authServer,
        private GetLogInActionHandler $getLogInActionHandler,
        private UserSessionRepository $userSessionRepository,
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

        $userSession = $this->userSessionRepository->findSessionFromCookies();

        if ($userSession === null) {
            $redirectUrl = $request->getUri()
                ->withScheme('https')
                ->withPort(null)
                ->__toString();

            return $this->getLogInActionHandler->renderAndCreateResponse(
                redirectUrl: $redirectUrl,
            );
        }

        $authRequest->setUser(new UserEntityForLeagueOauth(
            /** @phpstan-ignore-next-line */
            $userSession->user->email->toString(),
        ));

        /**
         * If we ever needed to ask the user for permission to authorize a
         * client, here is where we could set approved to false and then get
         * an error
         */
        $authRequest->setAuthorizationApproved(true);

        return $this->authServer->completeAuthorizationRequest(
            $authRequest,
            $response,
        );
    }
}
