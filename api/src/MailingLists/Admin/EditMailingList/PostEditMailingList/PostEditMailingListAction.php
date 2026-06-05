<?php

declare(strict_types=1);

namespace App\MailingLists\Admin\EditMailingList\PostEditMailingList;

use App\Auth\RequireEditMailingListsRoleMiddleware;
use App\MailingLists\MailingListsRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditMailingListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/mailing-lists/edit/{mailingListId}',
            self::class,
        )->add(RequireEditMailingListsRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MailingListFactory $mailingListFactory,
        private MailingListsRepository $mailingListsRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $mailingList = $this->mailingListFactory->createFromRequest(
            request: $request,
        );

        $result = $this->mailingListsRepository->persist(
            mailingList: $mailingList,
        );

        return $this->responder->respond(result: $result);
    }
}
