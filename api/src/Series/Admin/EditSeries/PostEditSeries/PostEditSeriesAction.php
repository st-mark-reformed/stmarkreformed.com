<?php

declare(strict_types=1);

namespace App\Series\Admin\EditSeries\PostEditSeries;

use App\Auth\RequireEditProfilesRoleMiddleware;
use App\Result\ResultResponder;
use App\Series\SeriesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/messages/series/edit/{seriesId}',
            self::class,
        )->add(RequireEditProfilesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private SeriesRepository $seriesRepository,
        private SeriesFactory $requestSeriesFactory,
        private UpdatedSeriesFactory $updatedSeriesFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $requestSeries = $this->requestSeriesFactory->createFromRequest(
            request: $request,
        );

        $persistentSeriesResult = $this->seriesRepository->findById(
            id: $requestSeries->id,
        );

        $updatedSeries = $this->updatedSeriesFactory->create(
            requestSeries: $requestSeries,
            persistentSeriesResult: $persistentSeriesResult,
        );

        $result = $this->seriesRepository->persist(series: $updatedSeries);

        return $this->responder->respond(result: $result);
    }
}
