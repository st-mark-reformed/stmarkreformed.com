<?php

declare(strict_types=1);

namespace App\Messages\Admin;

use App\Auth\RequireEditMessagesRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetHasEditMessagesRoleAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/messages/has-edit-messages-role',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write('{"hasRole": true}');

        return $response->withHeader('Content-type', 'application/json');
    }
}
