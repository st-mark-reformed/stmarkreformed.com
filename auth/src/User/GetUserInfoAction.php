<?php

declare(strict_types=1);

namespace App\User;

use App\ResourceServerMiddleware;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

use function json_encode;

readonly class GetUserInfoAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/userinfo', self::class)
            ->add(ResourceServerMiddleware::class);
    }

    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(
        ServerRequest $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $email = $request->attributes->getString('oauth_user_id');

        $user = $this->userRepository->findByEmail($email);

        $response->getBody()->write(
            (string) json_encode($user->asArray()),
        );

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
