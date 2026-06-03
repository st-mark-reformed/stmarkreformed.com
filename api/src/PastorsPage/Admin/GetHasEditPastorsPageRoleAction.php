<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin;

use App\Auth\RequireEditPastorsPageRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetHasEditPastorsPageRoleAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/pastors-page/has-edit-pastors-page-role',
            self::class,
        )->add(RequireEditPastorsPageRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write('{"hasRole": true}');

        return $response->withHeader('Content-type', 'application/json');
    }
}
