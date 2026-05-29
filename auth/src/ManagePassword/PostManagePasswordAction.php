<?php

declare(strict_types=1);

namespace App\ManagePassword;

use App\LogIn\GetLogInActionHandler;
use App\Url\AppUrlFactory;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use Slim\Csrf\Guard as CsrfGuard;

readonly class PostManagePasswordAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/manage-password', self::class)
            ->add(CsrfGuard::class);
    }

    public function __construct(
        private ChangePasswordWorkflow $changePasswordWorkflow,
        private PostDataFactory $postDataFactory,
        private UserSessionRepository $userSessionRepository,
        private GetLogInActionHandler $getLogInActionHandler,
        private AppUrlFactory $appUrlFactory,
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

        $this->changePasswordWorkflow->execute(
            session: $session,
            postData: $this->postDataFactory->createFromRequest($request),
        );

        return $response->withStatus(303)->withHeader(
            'Location',
            $this->appUrlFactory->create('/manage-password')->asString(),
        );
    }
}
