<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\TemplateEngineFactory;
use App\Url\AppUrlFactory;
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
                ->render(),
        );

        return $response;
    }
}
