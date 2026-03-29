<?php

declare(strict_types=1);

namespace App\Messages\Admin\EditMessage\GetEditMessage;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Messages\MessagesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditMessageAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/messages/edit/{messageId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private MessagesRepository $repository,
        private EditMessageResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $messageId = $request->attributes->getString(name: 'messageId');

        $profileResult = $this->repository->findById(id: $messageId);

        $responder = $this->responderFactory->create(result: $profileResult);

        return $responder->respond();
    }
}
