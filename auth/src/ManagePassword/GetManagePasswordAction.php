<?php

declare(strict_types=1);

namespace App\ManagePassword;

use App\Html\ButtonConfig;
use App\Html\ButtonRow;
use App\Html\ButtonRows;
use App\Html\Glyphs\Glyph;
use App\Html\HtmlFormInputConfig;
use App\Html\HtmlFormInputType;
use App\Html\HtmlPath;
use App\LogIn\GetLogInActionHandler;
use App\TemplateEngineFactory;
use App\Url\AppUrlFactory;
use App\Url\FeUrlFactory;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetManagePasswordAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/manage-password', self::class);
    }

    public function __construct(
        private AppUrlFactory $appUrlFactory,
        private FeUrlFactory $feUrlFactory,
        private UserSessionRepository $userSessionRepository,
        private GetLogInActionHandler $getLogInActionHandler,
        private TemplateEngineFactory $templateEngineFactory,
        private ManagePasswordFlashMessages $flashMessages,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $session = $this->userSessionRepository->findSessionFromCookies();

        if ($session === null) {
            return $this->getLogInActionHandler->renderAndCreateResponse(
                redirectUrl: $this->appUrlFactory
                    ->create('/manage-password')
                    ->asString(),
            );
        }

        $messages = $this->flashMessages->retrieveMessages();

        $response->getBody()->write(
            $this->templateEngineFactory->createWithCsrfTokens()
                ->templatePath(HtmlPath::HTML_FORM_LAYOUT)
                ->addVar('pageTitle', 'Manage Password')
                ->addVar('formAction', '/manage-password')
                ->addVar('errorMessages', $messages->ofType(MessageType::error))
                ->addVar(
                    'successMessages',
                    $messages->ofType(MessageType::success),
                )
                ->addVar('inputs', [
                    new HtmlFormInputConfig(
                        label: 'Current password',
                        name: 'current_password',
                        type: HtmlFormInputType::password,
                        required: true,
                    ),
                    new HtmlFormInputConfig(
                        label: 'New password',
                        name: 'new_password',
                        type: HtmlFormInputType::password,
                        required: true,
                    ),
                    new HtmlFormInputConfig(
                        label: 'Confirm new password',
                        name: 'confirm_password',
                        type: HtmlFormInputType::password,
                        required: true,
                    ),
                ])
                ->addVar('submitButtonConfig', new ButtonConfig(
                    content: 'Update Password',
                    glyph: Glyph::ArrowRight,
                ))
                ->addVar('footerButtonRows', new ButtonRows(rows: [
                    new ButtonRow(buttons: [
                        new ButtonConfig(
                            content: 'Back to Admin',
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
