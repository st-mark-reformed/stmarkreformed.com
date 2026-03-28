<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile\PostEditProfile;

use App\Auth\RequireEditProfilesRoleMiddleware;
use App\Profiles\ProfilesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditProfileAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/profiles/edit/{profileId}',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private ProfileFactory $requestProfileFactory,
        private ProfilesRepository $profilesRepository,
        private UpdatedProfileFactory $updatedProfileFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $requestProfile = $this->requestProfileFactory->createFromRequest(
            request: $request,
        );

        $persistentProfileResult = $this->profilesRepository->findById(
            id: $requestProfile->id,
        );

        $updatedProfile = $this->updatedProfileFactory->create(
            requestProfile: $requestProfile,
            persistentProfileResult: $persistentProfileResult,
        );

        $result = $this->profilesRepository->persist(profile: $updatedProfile);

        return $this->responder->respond(result: $result);
    }
}
