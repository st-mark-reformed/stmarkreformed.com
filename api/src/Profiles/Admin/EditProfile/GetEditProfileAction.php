<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile;

use App\Auth\RequireEditProfilesRoleMiddleware;
use App\Profiles\ProfilesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditProfileAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/profiles/edit/{profileId}',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __construct(
        private ProfilesRepository $profilesRepository,
        private GetEditProfileResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $profileId = $request->attributes->getString(name: 'profileId');

        $profileResult = $this->profilesRepository->findById(id: $profileId);

        $responder = $this->responderFactory->create(result: $profileResult);

        return $responder->respond();
    }
}
