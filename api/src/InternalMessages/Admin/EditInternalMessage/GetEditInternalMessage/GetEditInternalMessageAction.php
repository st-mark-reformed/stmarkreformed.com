<?php

declare(strict_types=1);

namespace App\InternalMessages\Admin\EditInternalMessage\GetEditInternalMessage;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalMessages\InternalMessagesRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditInternalMessageAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/internal-messages/edit/{internalMessageId}',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(
        private InternalMessagesRepository $repository,
        private EditInternalMessageResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $messageId = $request->attributes->getString(name: 'internalMessageId');

        $messageResult = $this->repository->findById(id: $messageId);

        $responder = $this->responderFactory->create(result: $messageResult);

        return $responder->respond();
    }
}
