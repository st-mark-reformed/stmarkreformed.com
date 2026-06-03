<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin;

use App\Auth\RequireEditPastorsPageRoleMiddleware;
use App\PastorsPage\PastorsPageRepository;
use App\Result\Result;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostDeletePastorsPageItemsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->delete(
            '/admin/pastors-page',
            self::class,
        )->add(RequireEditPastorsPageRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private PastorsPageRepository $repository,
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
