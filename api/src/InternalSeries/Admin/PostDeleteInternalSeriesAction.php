<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalSeries\InternalSeriesRepository;
use App\Result\Result;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostDeleteInternalSeriesAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->delete(
            '/admin/internal-messages/series',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private InternalSeriesRepository $repository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        /** @var string[] $ids */
        $ids = $request->parsedBody->attributes['items'];

        $result = new Result();

        foreach ($ids as $id) {
            $result = $this->repository->delete(id: $id);

            if (! $result->success) {
                break;
            }
        }

        return $this->responder->respond(result: $result);
    }
}
