<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin\EditPastorsPageItem\GetEditPastorsPageItem;

use App\Auth\RequireEditPastorsPageRoleMiddleware;
use App\PastorsPage\PastorsPageRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditPastorsPageItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/pastors-page/edit/{pastorsPageId}',
            self::class,
        )->add(RequireEditPastorsPageRoleMiddleware::class);
    }

    public function __construct(
        private PastorsPageRepository $repository,
        private EditPastorsPageItemResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $pastorsPageId = $request->attributes->getString(name: 'pastorsPageId');

        $pastorsPageItemResult = $this->repository->findById(id: $pastorsPageId);

        $responder = $this->responderFactory->create(
            result: $pastorsPageItemResult,
        );

        return $responder->respond();
    }
}
