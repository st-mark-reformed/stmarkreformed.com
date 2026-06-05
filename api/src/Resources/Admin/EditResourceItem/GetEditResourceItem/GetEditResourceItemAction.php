<?php

declare(strict_types=1);

namespace App\Resources\Admin\EditResourceItem\GetEditResourceItem;

use App\Auth\RequireEditResourcesRoleMiddleware;
use App\Resources\ResourcesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditResourceItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/resources/edit/{resourceId}',
            self::class,
        )->add(RequireEditResourcesRoleMiddleware::class);
    }

    public function __construct(
        private ResourcesRepository $repository,
        private EditResourceItemResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $resourceId = $request->attributes->getString(name: 'resourceId');

        $resourceItemResult = $this->repository->findById(id: $resourceId);

        $responder = $this->responderFactory->create(
            result: $resourceItemResult,
        );

        return $responder->respond();
    }
}
