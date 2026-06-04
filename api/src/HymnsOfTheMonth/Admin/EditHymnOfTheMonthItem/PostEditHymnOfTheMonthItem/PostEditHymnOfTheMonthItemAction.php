<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin\EditHymnOfTheMonthItem\PostEditHymnOfTheMonthItem;

use App\Auth\RequireEditHymnsOfTheMonthRoleMiddleware;
use App\HymnsOfTheMonth\HymnsOfTheMonthRepository;
use App\Result\Result;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditHymnOfTheMonthItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/hymns-of-the-month/edit/{hymnOfTheMonthId}',
            self::class,
        )->add(RequireEditHymnsOfTheMonthRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private HymnOfTheMonthItemFactory $hymnOfTheMonthItemFactory,
        private HymnsOfTheMonthRepository $repository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        try {
            $hymnOfTheMonthItem = $this->hymnOfTheMonthItemFactory
                ->createFromRequest(request: $request);
        } catch (RuntimeException $error) {
            return $this->responder->respond(
                result: new Result(success: false, errors: [$error->getMessage()]),
            );
        }

        $result = $this->repository->persist(
            hymnOfTheMonthItem: $hymnOfTheMonthItem,
        );

        return $this->responder->respond(result: $result);
    }
}
