<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\LogIn\GetLogInActionHandler;
use App\TemplateEngineFactory;
use App\Url\AppUrlFactory;
use App\User\UserRole;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class RequireManageUsersRoleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private UserSessionRepository $userSessionRepository,
        private GetLogInActionHandler $getLogInActionHandler,
        private TemplateEngineFactory $templateEngineFactory,
        private ResponseFactoryInterface $responseFactory,
        private AppUrlFactory $appUrlFactory,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $session = $this->userSessionRepository->findSessionFromCookies();

        if ($session === null) {
            return $this->getLogInActionHandler->renderAndCreateResponse(
                redirectUrl: $this->appUrlFactory
                    ->create($request->getUri()->getPath())
                    ->asString(),
            );
        }

        if (! $session->user->roles->has(UserRole::MANAGE_USERS)) {
            return $this->renderAccessDenied();
        }

        return $handler->handle($request);
    }

    private function renderAccessDenied(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(403);

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(__DIR__ . '/AccessDenied.phtml')
                ->addVar('pageTitle', 'Access Denied')
                ->addVar(
                    'homeUrl',
                    $this->appUrlFactory->create()->asString(),
                )
                ->render(),
        );

        return $response;
    }
}
