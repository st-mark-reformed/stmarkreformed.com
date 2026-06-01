<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\NewInternalSeries;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalSeries\InternalSeriesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewInternalSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/internal-messages/series/new',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewInternalSeriesFactory $newInternalSeriesFactory,
        private InternalSeriesRepository $internalSeriesRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newSeries = $this->newInternalSeriesFactory->createFromRequest(
            request: $request,
        );

        $result = $this->internalSeriesRepository->create(series: $newSeries);

        return $this->responder->respond(result: $result);
    }
}
