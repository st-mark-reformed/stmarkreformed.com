<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use App\MailingLists\Subscribers;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function assert;
use function implode;

/**
 * Writes the subscriber rows for a single mailing list. Subscribers are owned
 * by their list and rewritten wholesale, so callers replace the full set
 * (delete-all then insert) rather than diffing individual rows.
 */
readonly class WriteSubscribers
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function deleteForList(string $mailingListId): Result
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM mailing_list_subscribers WHERE mailing_list_id = :id',
        );

        $result = $statement->execute(['id' => $mailingListId]);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }

    public function insertForList(
        string $mailingListId,
        Subscribers $subscribers,
    ): Result {
        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO mailing_list_subscribers',
            '(id, mailing_list_id, name, email_address)',
            'VALUES (:id, :mailing_list_id, :name, :email_address)',
        ]));

        foreach ($subscribers->items as $subscriber) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);

            $result = $statement->execute([
                'id' => $id->toString(),
                'mailing_list_id' => $mailingListId,
                'name' => $subscriber->name,
                'email_address' => $subscriber->emailAddress,
            ]);

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
            }
        }

        return new Result();
    }
}
