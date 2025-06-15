<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use Throwable;

use function assert;
use function is_string;

readonly class PutProfileCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->put('/cms/profiles/{id}', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private ProfileRepository $repository,
        private ProfileEntityFactory $newProfileEntityFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $idString = $request->getAttribute('id');
        assert(is_string($idString));

        try {
            $id = Uuid::fromString($idString);
        } catch (Throwable) {
            $id = Uuid::uuid7();
        }

        $profile = $this->repository->findById($id);

        if ($profile === null) {
            return $response->withStatus(404)->withHeader(
                'Content-type',
                'application/json',
            );
        }

        $updatedProfile = $this->newProfileEntityFactory->fromServerRequest(
            $request,
        )->withId($id);

        $result = $this->repository->persist($updatedProfile);

        return $this->responder->respond($result);
    }
}
