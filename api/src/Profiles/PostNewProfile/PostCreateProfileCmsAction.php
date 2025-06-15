<?php

declare(strict_types=1);

namespace App\Profiles\PostNewProfile;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use App\Profiles\ProfileRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

readonly class PostCreateProfileCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/cms/profiles', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private ProfileRepository $repository,
        private NewProfileEntityFactory $newProfileEntityFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $newProfile = $this->newProfileEntityFactory->fromServerRequest(
            $request,
        );

        $result = $this->repository->createAndPersist($newProfile);

        return $this->responder->respond($result);
    }
}
