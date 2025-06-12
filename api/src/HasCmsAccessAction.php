<?php

declare(strict_types=1);

namespace App;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

readonly class HasCmsAccessAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/has-cms-access', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode(
            ['status' => 'OK'],
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
