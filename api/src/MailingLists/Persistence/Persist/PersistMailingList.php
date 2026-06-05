<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence\Persist;

use App\MailingLists\MailingList;
use App\MailingLists\Persistence\FindById;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistMailingList
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistMailingListToPdo $persistMailingListToPdo,
    ) {
    }

    public function persist(MailingList $mailingList): Result
    {
        if (! $mailingList->isValid) {
            return new Result(
                success: false,
                errors: $mailingList->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(mailingList: $mailingList);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->persistMailingListToPdo->persist(
                mailingList: $mailingList,
            );

            if (! $result->success) {
                throw $result;
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

    private function idIsValid(MailingList $mailingList): Result
    {
        $record = $this->findById->find(id: $mailingList->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['Mailing list with this ID does not exist'],
            );
        }

        return new Result();
    }
}
