<?php

declare(strict_types=1);

namespace App\Resources\Admin\EditResourceItem\PostEditResourceItem;

use App\Auth\RequireEditResourcesRoleMiddleware;
use App\Resources\ResourcesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditResourceItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/resources/edit/{resourceId}',
            self::class,
        )->add(RequireEditResourcesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private ResourceItemFactory $resourceItemFactory,
        private ResourcesRepository $resourcesRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $resourceItem = $this->resourceItemFactory->createFromRequest(
            request: $request,
        );

        $result = $this->resourcesRepository->persist(
            resourceItem: $resourceItem,
        );

        return $this->responder->respond(result: $result);
    }
}
