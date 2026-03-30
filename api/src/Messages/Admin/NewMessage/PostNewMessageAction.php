<?php

declare(strict_types=1);

namespace App\Messages\Admin\NewMessage;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Messages\MessagesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewMessageAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/messages/new',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewMessageFactory $newMessageFactory,
        private MessagesRepository $messagesRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        // TODO: deal with file uploads
        $newMessage = $this->newMessageFactory->createFromRequest(
            request: $request,
        );

        $result = $this->messagesRepository->create(message: $newMessage);

        return $this->responder->respond(result: $result);
    }
}
