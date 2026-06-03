<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin;

use App\Auth\RequireEditMenOfTheMarkRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetHasEditMenOfTheMarkRoleAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/men-of-the-mark/has-edit-role',
            self::class,
        )->add(RequireEditMenOfTheMarkRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write('{"hasRole": true}');

        return $response->withHeader('Content-type', 'application/json');
    }
}
