<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin;

use App\InternalSeries\InternalSeriesRepository;
use App\RespondWithJson;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetInternalSeriesDropdownAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/internal-series/dropdown-list',
            self::class,
        );
    }

    public function __construct(
        private InternalSeriesRepository $repository,
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
