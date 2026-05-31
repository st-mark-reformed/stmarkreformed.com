<?php

declare(strict_types=1);

namespace App;

use App\Html\ButtonConfig;
use App\Html\ButtonRow;
use App\Html\ButtonRows;
use App\Html\Glyphs\Glyph;
use App\LogIn\GetLogInActionHandler;
use App\Url\AppUrlFactory;
use App\Url\FeUrlFactory;
use App\User\UserRole;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetIndexAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/', self::class);
    }

    public function __construct(
        private FeUrlFactory $feUrlFactory,
        private AppUrlFactory $appUrlFactory,
        private UserSessionRepository $userSessionRepository,
        private GetLogInActionHandler $getLogInActionHandler,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $session = $this->userSessionRepository->findSessionFromCookies();

        if ($session === null) {
            return $this->getLogInActionHandler->renderAndCreateResponse(
                redirectUrl: $this->appUrlFactory->create()->asString(),
            );
        }

        $rows = [
            new ButtonRow(buttons: [
                new ButtonConfig(
                    content: 'Log Out',
                    href: $this->appUrlFactory->create('/log-out')->asString(),
                ),
            ]),
            new ButtonRow(buttons: [
                new ButtonConfig(
                    content: 'Manage Password',
                    href: $this->appUrlFactory
                        ->create('/manage-password')
                        ->asString(),
                    glyph: Glyph::ArrowRight,
                ),
            ]),
        ];

        if ($session->user->roles->has(UserRole::MANAGE_USERS)) {
            $rows[] = new ButtonRow(buttons: [
                new ButtonConfig(
                    content: 'Manage Users',
                    href: $this->appUrlFactory
                        ->create('/manage-users')
                        ->asString(),
                    glyph: Glyph::ArrowRight,
                ),
            ]);
        }

        $rows[] = new ButtonRow(buttons: [
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
        ]);

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(__DIR__ . '/Index.phtml')
                ->addVar('userEmail', $session->user->email->toString())
                ->addVar('buttonRows', new ButtonRows(rows: $rows))
                ->render(),
        );

        return $response;
    }
}
