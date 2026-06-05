<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence\Persist;

use App\MailingLists\MailingList;
use App\MailingLists\Persistence\WriteSubscribers;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistMailingListToPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private WriteSubscribers $writeSubscribers,
    ) {
    }

    public function persist(MailingList $mailingList): Result
    {
        $listResult = $this->updateList(mailingList: $mailingList);

        if (! $listResult->success) {
            return $listResult;
        }

        $deleteResult = $this->writeSubscribers->deleteForList(
            mailingListId: $mailingList->id->toString(),
        );

        if (! $deleteResult->success) {
            return $deleteResult;
        }

        return $this->writeSubscribers->insertForList(
            mailingListId: $mailingList->id->toString(),
            subscribers: $mailingList->subscribers,
        );
    }

    private function updateList(MailingList $mailingList): Result
    {
        $params = [
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
            'UPDATE mailing_lists',
            'SET',
            implode(', ', array_map(
                static fn (string $column): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $mailingList->id->toString();

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }
}
