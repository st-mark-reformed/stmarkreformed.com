<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin\NewInternalMessage;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalMessages\InternalMessagesRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewInternalMessageAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/internal-messages/new',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewInternalMessageFactory $newInternalMessageFactory,
        private InternalMessagesRepository $internalMessagesRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newMessage = $this->newInternalMessageFactory->createFromRequest(
            request: $request,
        );

        $result = $this->internalMessagesRepository->create(message: $newMessage);

        return $this->responder->respond(result: $result);
    }
}
