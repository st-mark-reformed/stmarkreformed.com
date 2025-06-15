<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

readonly class GetAllProfilesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/cms/profiles', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private ProfileRepository $repository)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $profiles = $this->repository->findAll();

        $response->getBody()->write(
            (string) json_encode($profiles->asScalar()),
        );

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
