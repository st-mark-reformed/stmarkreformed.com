<?php

declare(strict_types=1);

namespace App\Messages\Admin\EditMessage\PostEdtMessage;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Messages\Admin\EditMessage\GetEditMessage\UpdatedMessageFactory;
use App\Messages\MessagesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditMessageAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/messages/edit/{messageId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MessageFactory $messageFactory,
        private MessagesRepository $messagesRepository,
        private UpdatedMessageFactory $updatedMessageFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $requestMessage = $this->messageFactory->createFromRequest(
            request: $request,
        );

        $persistentMessageResult = $this->messagesRepository->findById(
            id: $requestMessage->id,
        );

        $updatedMessage = $this->updatedMessageFactory->create(
            requestMessage: $requestMessage,
            persistentMessageResult: $persistentMessageResult,
        );

        $result = $this->messagesRepository->persist(message: $updatedMessage);

        return $this->responder->respond(result: $result);
    }
}
