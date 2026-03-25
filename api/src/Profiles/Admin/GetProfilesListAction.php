<?php

declare(strict_types=1);

namespace App\Profiles\Admin;

use App\Auth\RequireEditProfilesRoleMiddleware;
use App\Profiles\ProfilesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class GetProfilesListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/profiles',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __construct(private ProfilesRepository $profilesRepository)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $profiles = $this->profilesRepository->findAll();

        $response->getBody()->write((string) json_encode(
            $profiles->asArray(),
        ));

        return $response->withHeader('Content-type', 'application/json');
    }
}
