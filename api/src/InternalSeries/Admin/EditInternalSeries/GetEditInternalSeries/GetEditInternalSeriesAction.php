<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\EditInternalSeries\GetEditInternalSeries;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalSeries\InternalSeriesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditInternalSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/internal-messages/series/edit/{seriesId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private InternalSeriesRepository $seriesRepository,
        private EditInternalSeriesResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $seriesId = $request->attributes->getString(name: 'seriesId');

        $seriesResult = $this->seriesRepository->findById(id: $seriesId);

        $responder = $this->responderFactory->create(result: $seriesResult);

        return $responder->respond();
    }
}
