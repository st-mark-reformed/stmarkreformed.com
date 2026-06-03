<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin\EditMenOfTheMarkItem\GetEditMenOfTheMarkItem;

use App\Auth\RequireEditMenOfTheMarkRoleMiddleware;
use App\MenOfTheMark\MenOfTheMarkRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditMenOfTheMarkItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/men-of-the-mark/edit/{id}',
            self::class,
        )->add(RequireEditMenOfTheMarkRoleMiddleware::class);
    }

    public function __construct(
        private MenOfTheMarkRepository $repository,
        private EditMenOfTheMarkItemResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $id = $request->attributes->getString(name: 'id');

        $result = $this->repository->findById(id: $id);

        $responder = $this->responderFactory->create(result: $result);

        return $responder->respond();
    }
}
