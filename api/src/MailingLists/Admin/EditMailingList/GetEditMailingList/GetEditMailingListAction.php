<?php

declare(strict_types=1);

namespace App\MailingLists\Admin\EditMailingList\GetEditMailingList;

use App\Auth\RequireEditMailingListsRoleMiddleware;
use App\MailingLists\MailingListsRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditMailingListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/mailing-lists/edit/{mailingListId}',
            self::class,
        )->add(RequireEditMailingListsRoleMiddleware::class);
    }

    public function __construct(
        private MailingListsRepository $repository,
        private EditMailingListResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $mailingListId = $request->attributes->getString(name: 'mailingListId');

        $mailingListResult = $this->repository->findById(id: $mailingListId);

        $responder = $this->responderFactory->create(
            result: $mailingListResult,
        );

        return $responder->respond();
    }
}
