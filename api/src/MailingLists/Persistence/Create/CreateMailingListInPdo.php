<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence\Create;

use App\EmptyUuid;
use App\MailingLists\NewMailingList;
use App\MailingLists\Persistence\WriteSubscribers;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class CreateMailingListInPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
        private WriteSubscribers $writeSubscribers,
    ) {
    }

    public function create(NewMailingList $mailingList): Result
    {
        $id = $this->resolveId(id: $mailingList->id);

        $listResult = $this->insertList(id: $id, mailingList: $mailingList);

        if (! $listResult->success) {
            return $listResult;
        }

        return $this->writeSubscribers->insertForList(
            mailingListId: $id->toString(),
            subscribers: $mailingList->subscribers,
        );
    }

    private function insertList(
        UuidInterface $id,
        NewMailingList $mailingList,
    ): Result {
        $params = [
            'id' => $id->toString(),
            'list_name' => $mailingList->listName,
            'list_address' => $mailingList->listAddress,
            'imap_server' => $mailingList->imapServer,
            'imap_port' => (string) $mailingList->imapPort,
            'connection_type' => $mailingList->connectionType->value,
            'username' => $mailingList->username,
            'password' => $mailingList->password,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO mailing_lists (' . implode(', ', $columns) . ')',
            'VALUES (:' . implode(', :', $columns) . ')',
        ]));

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }

    private function resolveId(UuidInterface $id): UuidInterface
    {
        if (! $id instanceof EmptyUuid) {
            return $id;
        }

        /** @phpstan-ignore-next-line */
        $generated = $this->uuidFactory->uuid7();
        assert($generated instanceof UuidInterface);

        return $generated;
    }
}
