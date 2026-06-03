<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin\NewMenOfTheMarkItem;

use App\Auth\RequireEditMenOfTheMarkRoleMiddleware;
use App\MenOfTheMark\MenOfTheMarkRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewMenOfTheMarkItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/men-of-the-mark/new',
            self::class,
        )->add(RequireEditMenOfTheMarkRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewMenOfTheMarkItemFactory $factory,
        private MenOfTheMarkRepository $repository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newItem = $this->factory->createFromRequest(request: $request);

        $result = $this->repository->create(menOfTheMarkItem: $newItem);

        return $this->responder->respond(result: $result);
    }
}
