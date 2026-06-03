<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin\EditPastorsPageItem\PostEditPastorsPageItem;

use App\Auth\RequireEditPastorsPageRoleMiddleware;
use App\PastorsPage\PastorsPageRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditPastorsPageItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/pastors-page/edit/{pastorsPageId}',
            self::class,
        )->add(RequireEditPastorsPageRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private PastorsPageItemFactory $pastorsPageItemFactory,
        private PastorsPageRepository $pastorsPageRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $pastorsPageItem = $this->pastorsPageItemFactory->createFromRequest(
            request: $request,
        );

        $result = $this->pastorsPageRepository->persist(
            pastorsPageItem: $pastorsPageItem,
        );

        return $this->responder->respond(result: $result);
    }
}
