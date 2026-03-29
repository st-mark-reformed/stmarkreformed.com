<?php

declare(strict_types=1);

namespace App\Messages\Admin;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Messages\MessagesRepository;
use App\RespondWithJson;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

readonly class GetMessagesListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/messages',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private MessagesRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $messages = $this->repository->findAll();

        return new RespondWithJson(
            entity: $messages,
            factory: $this->factory,
        )->respond();
    }
}
