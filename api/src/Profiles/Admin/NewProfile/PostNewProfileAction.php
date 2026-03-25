<?php

declare(strict_types=1);

namespace App\Profiles\Admin\NewProfile;

use App\Auth\RequireEditProfilesRoleMiddleware;
use App\Profiles\ProfilesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewProfileAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/profiles/new',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __construct(
        private NewProfileFactory $newProfileFactory,
        private ResultResponder $responder,
        private ProfilesRepository $profilesRepository,
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $newProfile = $this->newProfileFactory->createFromRequest(
            request: $request,
        );

        $result = $this->profilesRepository->create(
            newProfile: $newProfile,
        );

        return $this->responder->respond(result: $result);
    }
}
