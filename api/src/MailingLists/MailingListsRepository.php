<?php

declare(strict_types=1);

namespace App\MailingLists;

use App\MailingLists\Persistence\Create\CreateMailingList;
use App\MailingLists\Persistence\Delete\DeleteMailingList;
use App\MailingLists\Persistence\FindAll;
use App\MailingLists\Persistence\FindById;
use App\MailingLists\Persistence\FindSubscribers;
use App\MailingLists\Persistence\Persist\PersistMailingList;
use App\MailingLists\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class MailingListsRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private FindSubscribers $findSubscribers,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateMailingList $createMailingList,
        private DeleteMailingList $deleteMailingList,
        private PersistMailingList $persistMailingList,
    ) {
    }

    public function create(NewMailingList $mailingList): Result
    {
        return $this->createMailingList->create(mailingList: $mailingList);
    }

    public function delete(string|UuidInterface $id): Result
    {
        $mailingListResult = $this->findById(id: $id);

        if (! $mailingListResult->hasMailingList) {
            return new Result();
        }

        return $this->deleteMailingList->delete(
            mailingList: $mailingListResult->mailingList,
        );
    }

    public function persist(MailingList $mailingList): Result
    {
        return $this->persistMailingList->persist(mailingList: $mailingList);
    }

    public function findAll(): MailingLists
    {
        return $this->transformer->toEntities(
            records: $this->findAll->find(),
            subscriberRecords: $this->findSubscribers->all(),
        );
    }

    public function findById(string|UuidInterface $id): MailingListResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new MailingListResult();
        }

        $subscribers = $this->transformer->subscribersFromRecords(
            $this->findSubscribers->forListId(mailingListId: $record->id),
        );

        return new MailingListResult(
            mailingList: $this->transformer->toEntity(
                record: $record,
                subscribers: $subscribers,
            ),
        );
    }
}
