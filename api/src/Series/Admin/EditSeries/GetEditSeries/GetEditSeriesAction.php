<?php

declare(strict_types=1);

namespace App\Series\Admin\EditSeries\GetEditSeries;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Series\SeriesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/messages/series/edit/{seriesId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private SeriesRepository $seriesRepository,
        private EditSeriesResponderFactory $responderFactory,
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
