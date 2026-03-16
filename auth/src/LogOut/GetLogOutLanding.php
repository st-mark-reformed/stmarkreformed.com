<?php

declare(strict_types=1);

namespace App\LogOut;

use App\NoticePage\NoticePage;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetLogOutLanding
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->any('/log-out/landing', self::class);
    }

    public function __construct(
        private NoticePage $noticePage,
        private UserSessionRepository $userSessionRepository,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $session = $this->userSessionRepository->findSessionFromCookies();

        if ($session !== null) {
            return $this->noticePage->generateHttpResponse(
                pageTitle: 'You Were Not Logged Out',
                message: 'We were not able to log you out. Please try again.',
                buttonText: 'Try Again',
                buttonUrl: '/log-out',
            );
        }

        return $this->noticePage->generateHttpResponse(
            pageTitle: 'Logged Out',
            message: 'You have been logged out successfully.',
            buttonText: 'Log In',
            buttonUrl: '/',
        );
    }
}
