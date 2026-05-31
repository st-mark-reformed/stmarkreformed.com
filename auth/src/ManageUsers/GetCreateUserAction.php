<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\Html\ButtonConfig;
use App\Html\ButtonRow;
use App\Html\ButtonRows;
use App\Html\Glyphs\Glyph;
use App\Html\HtmlFormInputConfig;
use App\Html\HtmlFormInputType;
use App\Html\HtmlPath;
use App\TemplateEngineFactory;
use App\Url\AppUrlFactory;
use App\User\UserRoles;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetCreateUserAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/manage-users/create', self::class)
            ->add(RequireManageUsersRoleMiddleware::class);
    }

    public function __construct(
        private AppUrlFactory $appUrlFactory,
        private TemplateEngineFactory $templateEngineFactory,
        private RoleCheckboxesFactory $roleCheckboxesFactory,
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
                ->templatePath(HtmlPath::HTML_FORM_LAYOUT)
                ->addVar('pageTitle', 'Create User')
                ->addVar('formAction', '/manage-users/create')
                ->addVar('errorMessages', $messages->ofType(MessageType::error))
                ->addVar(
                    'successMessages',
                    $messages->ofType(MessageType::success),
                )
                ->addVar('inputs', [
                    new HtmlFormInputConfig(
                        label: 'Email address',
                        name: 'email',
                        type: HtmlFormInputType::email,
                        required: true,
                    ),
                    new HtmlFormInputConfig(
                        label: 'Password',
                        name: 'password',
                        type: HtmlFormInputType::password,
                        required: true,
                    ),
                    new HtmlFormInputConfig(
                        label: 'Confirm password',
                        name: 'confirm_password',
                        type: HtmlFormInputType::password,
                        required: true,
                    ),
                    ...$this->roleCheckboxesFactory->create(new UserRoles()),
                ])
                ->addVar('submitButtonConfig', new ButtonConfig(
                    content: 'Create User',
                    glyph: Glyph::ArrowRight,
                ))
                ->addVar('footerButtonRows', new ButtonRows(rows: [
                    new ButtonRow(buttons: [
                        new ButtonConfig(
                            content: 'Back to Users',
                            href: $this->appUrlFactory
                                ->create('/manage-users')
                                ->asString(),
                        ),
                    ]),
                ]))
                ->render(),
        );

        return $response;
    }
}
