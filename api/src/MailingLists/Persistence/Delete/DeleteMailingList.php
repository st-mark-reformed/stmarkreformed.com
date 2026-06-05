<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence\Delete;

use App\MailingLists\MailingList;
use App\MailingLists\Persistence\WriteSubscribers;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeleteMailingList
{
    public function __construct(
        private ApiPdo $pdo,
        private WriteSubscribers $writeSubscribers,
    ) {
    }

    public function delete(MailingList $mailingList): Result
    {
        try {
            $this->pdo->beginTransaction();

            $subscribersResult = $this->writeSubscribers->deleteForList(
                mailingListId: $mailingList->id->toString(),
            );

            if (! $subscribersResult->success) {
                throw $subscribersResult;
            }

            $statement = $this->pdo->prepare(
                'DELETE FROM mailing_lists WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $mailingList->id->toString()],
            );

            if (! $result) {
                throw new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
            }

            $this->pdo->commit();

            return new Result();
        } catch (Throwable $error) {
            $this->pdo->rollBack();

            if ($error instanceof Result) {
                return $error;
            }

            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }
    }
}
