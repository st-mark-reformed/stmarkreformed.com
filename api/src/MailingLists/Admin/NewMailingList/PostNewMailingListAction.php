<?php

declare(strict_types=1);

namespace App\MailingLists\Admin\NewMailingList;

use App\Auth\RequireEditMailingListsRoleMiddleware;
use App\MailingLists\MailingListsRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewMailingListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/mailing-lists/new',
            self::class,
        )->add(RequireEditMailingListsRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewMailingListFactory $newMailingListFactory,
        private MailingListsRepository $mailingListsRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newMailingList = $this->newMailingListFactory->createFromRequest(
            request: $request,
        );

        $result = $this->mailingListsRepository->create(
            mailingList: $newMailingList,
        );

        return $this->responder->respond(result: $result);
    }
}
