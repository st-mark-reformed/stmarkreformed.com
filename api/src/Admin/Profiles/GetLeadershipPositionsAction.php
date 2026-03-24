<?php

declare(strict_types=1);

namespace App\Admin\Profiles;

use App\Auth\RequireEditProfilesRoleMiddleware;
use App\Profiles\ProfileLeadershipPosition;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function array_map;
use function json_encode;

readonly class GetLeadershipPositionsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/profiles/leadership-positions',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode(array_map(
            static function (ProfileLeadershipPosition $position): array {
                return [
                    'name' => $position->name,
                    'label' => $position->humanReadable(),
                ];
            },
            ProfileLeadershipPosition::cases(),
        )));

        return $response->withHeader('Content-type', 'application/json');
    }
}
