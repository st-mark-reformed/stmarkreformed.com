<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin\EditMenOfTheMarkItem\PostEditMenOfTheMarkItem;

use App\Auth\RequireEditMenOfTheMarkRoleMiddleware;
use App\MenOfTheMark\MenOfTheMarkRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditMenOfTheMarkItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/men-of-the-mark/edit/{id}',
            self::class,
        )->add(RequireEditMenOfTheMarkRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MenOfTheMarkItemFactory $factory,
        private MenOfTheMarkRepository $repository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $item = $this->factory->createFromRequest(request: $request);

        $result = $this->repository->persist(menOfTheMarkItem: $item);

        return $this->responder->respond(result: $result);
    }
}
