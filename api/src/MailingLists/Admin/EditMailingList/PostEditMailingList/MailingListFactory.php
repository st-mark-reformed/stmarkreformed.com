<?php

declare(strict_types=1);

namespace App\MailingLists\Admin\EditMailingList\PostEditMailingList;

use App\EmptyUuid;
use App\MailingLists\Admin\SubscriberResolver;
use App\MailingLists\ConnectionType;
use App\MailingLists\MailingList;
use App\MailingLists\MailingListsRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class MailingListFactory
{
    public function __construct(
        private SubscriberResolver $subscriberResolver,
        private MailingListsRepository $repository,
    ) {
    }

    public function createFromRequest(ServerRequest $request): MailingList
    {
        $id = $this->resolveId(request: $request);

        return new MailingList(
            id: $id,
            listName: $request->parsedBody->getString(name: 'listName'),
            listAddress: $request->parsedBody->getString(name: 'listAddress'),
            imapServer: $request->parsedBody->getString(name: 'imapServer'),
            imapPort: $request->parsedBody->getInt(name: 'imapPort'),
            connectionType: ConnectionType::fromString(
                $request->parsedBody->getString(name: 'connectionType'),
            ),
            username: $request->parsedBody->getString(name: 'username'),
            password: $this->resolvePassword(request: $request, id: $id),
            subscribers: $this->subscriberResolver->resolve(request: $request),
        );
    }

    private function resolveId(ServerRequest $request): UuidInterface
    {
        try {
            return Uuid::fromString(
                $request->attributes->getString(name: 'mailingListId'),
            );
        } catch (Throwable) {
            return new EmptyUuid();
        }
    }

    /**
     * The IMAP password is never sent to the browser, so a blank submission
     * means "keep the stored password" rather than clearing it.
     */
    private function resolvePassword(
        ServerRequest $request,
        UuidInterface $id,
    ): string {
        $submitted = $request->parsedBody->getString(name: 'password');

        if ($submitted !== '') {
            return $submitted;
        }

        $result = $this->repository->findById(id: $id);

        return $result->hasMailingList ? $result->mailingList->password : '';
    }
}
