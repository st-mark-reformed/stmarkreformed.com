<?php

declare(strict_types=1);

namespace App\Profiles\Admin;

use App\Profiles\ProfilesRepository;
use App\RespondWithJson;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetProfilesDropdownValues
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/admin/profiles/dropdown-list', self::class);
    }

    public function __construct(
        private ProfilesRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $profiles = $this->repository->findAll();

        return new RespondWithJson(
            entity: $profiles->asDropdownList(),
            factory: $this->factory,
        )->respond();
    }
}
