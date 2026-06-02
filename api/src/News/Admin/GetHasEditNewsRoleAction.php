<?php

declare(strict_types=1);

namespace App\News\Admin;

use App\Auth\RequireEditNewsRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetHasEditNewsRoleAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/news/has-edit-news-role',
            self::class,
        )->add(RequireEditNewsRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write('{"hasRole": true}');

        return $response->withHeader('Content-type', 'application/json');
    }
}
