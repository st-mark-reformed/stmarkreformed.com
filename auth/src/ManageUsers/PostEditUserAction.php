<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\Url\AppUrlFactory;
use App\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use Slim\Csrf\Guard as CsrfGuard;

use function is_string;

readonly class PostEditUserAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/manage-users/{id}/edit', self::class)
            ->add(CsrfGuard::class)
            ->add(RequireManageUsersRoleMiddleware::class);
    }

    public function __construct(
        private UserRepository $userRepository,
        private EditUserWorkflow $editUserWorkflow,
        private EditUserPostDataFactory $postDataFactory,
        private ManageUsersFlashMessages $flashMessages,
        private AppUrlFactory $appUrlFactory,
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

            return $this->redirect($response, '/manage-users');
        }

        $success = $this->editUserWorkflow->execute(
            $user,
            $this->postDataFactory->createFromRequest($request),
        );

        if ($success) {
            return $this->redirect($response, '/manage-users');
        }

        return $this->redirect($response, '/manage-users/' . $id . '/edit');
    }

    private function redirect(
        ResponseInterface $response,
        string $location,
    ): ResponseInterface {
        return $response->withStatus(303)->withHeader(
            'Location',
            $this->appUrlFactory->create($location)->asString(),
        );
    }
}
