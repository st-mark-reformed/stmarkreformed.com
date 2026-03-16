<?php

declare(strict_types=1);

namespace App\LogOut;

use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class LogOutAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->any('/log-out', self::class);
    }

    public function __construct(
        private UserSessionRepository $userSessionRepository,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->userSessionRepository->deleteSessionFromCookies();

        return $response->withStatus(303)->withHeader(
            'Location',
            '/log-out/landing',
        );
    }
}
