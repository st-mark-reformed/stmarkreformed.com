<?php

declare(strict_types=1);

namespace App\Admin\Profiles;

use App\Auth\RequireEditProfilesRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class GetAdminProfilesListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/profiles',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write(
            (string) json_encode(['foo' => 'bar']),
        );

        return $response->withHeader('Content-type', 'application/json');
    }
}
