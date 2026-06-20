<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\Html\ButtonConfig;
use App\Html\ButtonRow;
use App\Html\ButtonRows;
use App\Html\Glyphs\Glyph;
use App\Html\Glyphs\GlyphPosition;
use App\TemplateEngineFactory;
use App\Url\AppUrlFactory;
use App\Url\FeUrlFactory;
use App\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function rtrim;

readonly class GetManageUsersAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/manage-users', self::class)
            ->add(RequireManageUsersRoleMiddleware::class);
    }

    public function __construct(
        private UserRepository $userRepository,
        private AppUrlFactory $appUrlFactory,
        private FeUrlFactory $feUrlFactory,
        private TemplateEngineFactory $templateEngineFactory,
        private ManageUsersFlashMessages $flashMessages,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $messages = $this->flashMessages->retrieveMessages();

        $response->getBody()->write(
            $this->templateEngineFactory->createWithCsrfTokens()
                ->templatePath(__DIR__ . '/ManageUsers.phtml')
                ->addVar('pageTitle', 'Manage Users')
                ->addVar('users', $this->userRepository->all())
                ->addVar('errorMessages', $messages->ofType(MessageType::error))
                ->addVar(
                    'successMessages',
                    $messages->ofType(MessageType::success),
                )
                ->addVar(
                    'createUrl',
                    $this->appUrlFactory->create('/manage-users/create')
                        ->asString(),
                )
                ->addVar(
                    'baseUrl',
                    rtrim(
                        $this->appUrlFactory->create('/manage-users')->asString(),
                        '/',
                    ),
                )
                ->addVar('footerButtonRows', new ButtonRows(rows: [
                    new ButtonRow(buttons: [
                        new ButtonConfig(
                            content: 'Back to Dashboard',
                            href: $this->appUrlFactory->create('/')->asString(),
                            glyph: Glyph::ArrowLeft,
                            glyphPosition: GlyphPosition::Left,
                        ),
                        new ButtonConfig(
                            content: 'Go to Admin',
                            href: $this->feUrlFactory
                                ->create(uri: '/admin')
                                ->asString(),
                            glyph: Glyph::ArrowRight,
                        ),
                    ]),
                ]))
                ->render(),
        );

        return $response;
    }
}
