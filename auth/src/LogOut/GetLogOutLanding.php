<?php

declare(strict_types=1);

namespace App\LogOut;

use App\Html\ButtonConfig;
use App\Html\ButtonRow;
use App\Html\ButtonRows;
use App\Html\Glyphs\Glyph;
use App\NoticePage\NoticePage;
use App\Url\FeUrlFactory;
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
        private FeUrlFactory $feUrlFactory,
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
                buttonRows: new ButtonRows(rows: [
                    new ButtonRow(buttons: [
                        new ButtonConfig(
                            content: 'Try Again',
                            href: '/log-out',
                        ),
                    ]),
                ]),
            );
        }

        return $this->noticePage->generateHttpResponse(
            pageTitle: 'Logged Out',
            message: 'You have been logged out successfully.',
            buttonRows: new ButtonRows(rows: [
                new ButtonRow(buttons: [
                    new ButtonConfig(
                        content: 'Log In Again',
                        href: '/',
                    ),
                ]),
                new ButtonRow(buttons: [
                    new ButtonConfig(
                        content: 'Go to Admin',
                        href: $this->feUrlFactory->create(uri: '/admin')->asString(),
                        glyph: Glyph::ArrowRight,
                    ),
                    new ButtonConfig(
                        content: 'Go to Site',
                        href: $this->feUrlFactory->create()->asString(),
                        glyph: Glyph::ArrowRight,
                    ),
                ]),
            ]),
        );
    }
}
