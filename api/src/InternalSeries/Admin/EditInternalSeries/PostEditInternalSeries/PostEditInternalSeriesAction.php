<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\EditInternalSeries\PostEditInternalSeries;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalSeries\InternalSeriesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditInternalSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/internal-messages/series/edit/{seriesId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private InternalSeriesRepository $seriesRepository,
        private InternalSeriesFactory $requestSeriesFactory,
        private UpdatedInternalSeriesFactory $updatedSeriesFactory,
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
