<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin;

use App\Auth\RequireEditHymnsOfTheMonthRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetHasEditHymnsOfTheMonthRoleAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/hymns-of-the-month/has-edit-hymns-of-the-month-role',
            self::class,
        )->add(RequireEditHymnsOfTheMonthRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write('{"hasRole": true}');

        return $response->withHeader('Content-type', 'application/json');
    }
}
