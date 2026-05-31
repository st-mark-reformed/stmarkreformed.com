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
use App\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function is_string;

readonly class GetResetPasswordAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/manage-users/{id}/password', self::class)
            ->add(RequireManageUsersRoleMiddleware::class);
    }

    public function __construct(
        private UserRepository $userRepository,
        private AppUrlFactory $appUrlFactory,
        private TemplateEngineFactory $templateEngineFactory,
        private ManageUsersFlashMessages $flashMessages,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $id = $request->getAttribute('id');
        $id = is_string($id) ? $id : '';

        $user = $this->userRepository->findById($id);

        if (! $user->isValid) {
            $this->flashMessages->sendError('That user could not be found.');

            return $response->withStatus(303)->withHeader(
                'Location',
                $this->appUrlFactory->create('/manage-users')->asString(),
            );
        }

        $messages = $this->flashMessages->retrieveMessages();

        $response->getBody()->write(
            $this->templateEngineFactory->createWithCsrfTokens()
                ->templatePath(HtmlPath::HTML_FORM_LAYOUT)
                ->addVar('pageTitle', 'Reset Password')
                ->addVar('h2', 'Reset password for ' . $user->email->toString())
                ->addVar('formAction', '/manage-users/' . $id . '/password')
                ->addVar('errorMessages', $messages->ofType(MessageType::error))
                ->addVar(
                    'successMessages',
                    $messages->ofType(MessageType::success),
                )
                ->addVar('inputs', [
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
                    content: 'Reset Password',
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
