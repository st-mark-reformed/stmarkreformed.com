<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\Url\AppUrlFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use Slim\Csrf\Guard as CsrfGuard;

readonly class PostCreateUserAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/manage-users/create', self::class)
            ->add(CsrfGuard::class)
            ->add(RequireManageUsersRoleMiddleware::class);
    }

    public function __construct(
        private CreateUserWorkflow $createUserWorkflow,
        private CreateUserPostDataFactory $postDataFactory,
        private AppUrlFactory $appUrlFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $success = $this->createUserWorkflow->execute(
            $this->postDataFactory->createFromRequest($request),
        );

        $location = $success ? '/manage-users' : '/manage-users/create';

        return $response->withStatus(303)->withHeader(
            'Location',
            $this->appUrlFactory->create($location)->asString(),
        );
    }
}
