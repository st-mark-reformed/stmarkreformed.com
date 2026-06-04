<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin\NewHymnOfTheMonthItem;

use App\Auth\RequireEditHymnsOfTheMonthRoleMiddleware;
use App\HymnsOfTheMonth\HymnsOfTheMonthRepository;
use App\Result\Result;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewHymnOfTheMonthItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/hymns-of-the-month/new',
            self::class,
        )->add(RequireEditHymnsOfTheMonthRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewHymnOfTheMonthItemFactory $newHymnOfTheMonthItemFactory,
        private HymnsOfTheMonthRepository $repository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        try {
            $newHymnOfTheMonthItem = $this->newHymnOfTheMonthItemFactory
                ->createFromRequest(request: $request);
        } catch (RuntimeException $error) {
            return $this->responder->respond(
                result: new Result(success: false, errors: [$error->getMessage()]),
            );
        }

        $result = $this->repository->create(
            hymnOfTheMonthItem: $newHymnOfTheMonthItem,
        );

        return $this->responder->respond(result: $result);
    }
}
