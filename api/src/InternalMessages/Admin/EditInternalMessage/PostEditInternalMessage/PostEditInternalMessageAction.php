<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin\EditInternalMessage\PostEditInternalMessage;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalMessages\InternalMessagesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditInternalMessageAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/internal-messages/edit/{internalMessageId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private InternalMessageFactory $messageFactory,
        private InternalMessagesRepository $internalMessagesRepository,
        private UpdatedInternalMessageFactory $updatedInternalMessageFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $requestMessage = $this->messageFactory->createFromRequest(
            request: $request,
        );

        $persistentMessageResult = $this->internalMessagesRepository->findById(
            id: $requestMessage->id,
        );

        $updatedMessage = $this->updatedInternalMessageFactory->create(
            requestMessage: $requestMessage,
            persistentMessageResult: $persistentMessageResult,
        );

        $result = $this->internalMessagesRepository->persist(
            message: $updatedMessage,
        );

        return $this->responder->respond(result: $result);
    }
}
