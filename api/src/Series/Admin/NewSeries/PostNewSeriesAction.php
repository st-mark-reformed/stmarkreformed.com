<?php

declare(strict_types=1);

namespace App\Series\Admin\NewSeries;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Result\ResultResponder;
use App\Series\SeriesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/messages/series/new',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewSeriesFactory $newSeriesFactory,
        private SeriesRepository $seriesRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newSeries = $this->newSeriesFactory->createFromRequest(
            request: $request,
        );

        $result = $this->seriesRepository->create(series: $newSeries);

        return $this->responder->respond(result: $result);
    }
}
