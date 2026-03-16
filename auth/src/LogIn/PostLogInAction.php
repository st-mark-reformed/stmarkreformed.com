<?php

declare(strict_types=1);

namespace App\LogIn;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use Slim\Csrf\Guard as CsrfGuard;

readonly class PostLogInAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/log-in', self::class);

        $routes->post('/log-in-tmp', self::class)
            ->add(CsrfGuard::class);
    }

    public function __construct(
        private LogInLocally $logInLocally,
        private PostDataFactory $postDataFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $postData = $this->postDataFactory->createFromRequest(
            $request,
        );

        $this->logInLocally->execute($postData);

        return $response->withStatus(303)->withHeader(
            'Location',
            $postData->redirectUrl,
        );
    }
}
