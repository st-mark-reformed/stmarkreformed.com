<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\Url\AppUrlFactory;
use App\User\UserRepository;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use Slim\Csrf\Guard as CsrfGuard;

use function is_string;

readonly class PostDeleteUserAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/manage-users/{id}/delete', self::class)
            ->add(CsrfGuard::class)
            ->add(RequireManageUsersRoleMiddleware::class);
    }

    public function __construct(
        private UserRepository $userRepository,
        private UserSessionRepository $userSessionRepository,
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

            return $this->redirectToList($response);
        }

        $session = $this->userSessionRepository->findSessionFromCookies();

        if ($session !== null && $session->user->id->toString() === $id) {
            $this->flashMessages->sendError(
                'You cannot delete your own account.',
            );

            return $this->redirectToList($response);
        }

        $this->userRepository->deleteUserById($id);

        $this->flashMessages->sendSuccess(
            'User "' . $user->email->toString() . '" was deleted.',
        );

        return $this->redirectToList($response);
    }

    private function redirectToList(
        ResponseInterface $response,
    ): ResponseInterface {
        return $response->withStatus(303)->withHeader(
            'Location',
            $this->appUrlFactory->create('/manage-users')->asString(),
        );
    }
}
