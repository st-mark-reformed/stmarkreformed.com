<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin\NewPastorsPageItem;

use App\Auth\RequireEditPastorsPageRoleMiddleware;
use App\PastorsPage\PastorsPageRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewPastorsPageItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/pastors-page/new',
            self::class,
        )->add(RequireEditPastorsPageRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewPastorsPageItemFactory $newPastorsPageItemFactory,
        private PastorsPageRepository $pastorsPageRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newPastorsPageItem = $this->newPastorsPageItemFactory->createFromRequest(
            request: $request,
        );

        $result = $this->pastorsPageRepository->create(
            pastorsPageItem: $newPastorsPageItem,
        );

        return $this->responder->respond(result: $result);
    }
}
