<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin\EditHymnOfTheMonthItem\GetEditHymnOfTheMonthItem;

use App\Auth\RequireEditHymnsOfTheMonthRoleMiddleware;
use App\HymnsOfTheMonth\HymnsOfTheMonthRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditHymnOfTheMonthItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/hymns-of-the-month/edit/{hymnOfTheMonthId}',
            self::class,
        )->add(RequireEditHymnsOfTheMonthRoleMiddleware::class);
    }

    public function __construct(
        private HymnsOfTheMonthRepository $repository,
        private EditHymnOfTheMonthItemResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $hymnOfTheMonthId = $request->attributes->getString(
            name: 'hymnOfTheMonthId',
        );

        $hymnOfTheMonthItemResult = $this->repository->findById(
            id: $hymnOfTheMonthId,
        );

        $responder = $this->responderFactory->create(
            result: $hymnOfTheMonthItemResult,
        );

        return $responder->respond();
    }
}
