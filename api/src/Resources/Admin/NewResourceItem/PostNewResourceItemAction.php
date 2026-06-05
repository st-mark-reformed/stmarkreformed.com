<?php

declare(strict_types=1);

namespace App\Resources\Admin\NewResourceItem;

use App\Auth\RequireEditResourcesRoleMiddleware;
use App\Resources\ResourcesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewResourceItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/resources/new',
            self::class,
        )->add(RequireEditResourcesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewResourceItemFactory $newResourceItemFactory,
        private ResourcesRepository $resourcesRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newResourceItem = $this->newResourceItemFactory->createFromRequest(
            request: $request,
        );

        $result = $this->resourcesRepository->create(
            resourceItem: $newResourceItem,
        );

        return $this->responder->respond(result: $result);
    }
}
