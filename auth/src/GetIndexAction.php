<?php

declare(strict_types=1);

namespace App;

use App\LogIn\GetLogInActionHandler;
use App\Url\AppUrlFactory;
use App\User\UserSessionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetIndexAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/', self::class);
    }

    public function __construct(
        private AppUrlFactory $appUrlFactory,
        private UserSessionRepository $userSessionRepository,
        private GetLogInActionHandler $getLogInActionHandler,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $session = $this->userSessionRepository->findSessionFromCookies();

        if ($session === null) {
            return $this->getLogInActionHandler->renderAndCreateResponse(
                $this->appUrlFactory->create()->asString(),
            );
        }

        $response->getBody()->write(
            $this->templateEngineFactory->create()
                ->templatePath(__DIR__ . '/Index.phtml')
                ->addVar('userEmail', $session->user->email->toString())
                ->render(),
        );

        return $response;
    }
}
