<?php

declare(strict_types=1);

namespace App\Series\Admin;

use App\RespondWithJson;
use App\Series\SeriesRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetSeriesDropdownAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/series/dropdown-list',
            self::class,
        );
    }

    public function __construct(
        private SeriesRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $seriesCollection = $this->repository->findAll();

        return new RespondWithJson(
            entity: $seriesCollection->asDropdownList(),
            factory: $this->factory,
        )->respond();
    }
}
