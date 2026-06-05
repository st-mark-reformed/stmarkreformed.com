<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence\Create;

use App\MailingLists\NewMailingList;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreateMailingList
{
    public function __construct(
        private ApiPdo $pdo,
        private CreateMailingListInPdo $createMailingListInPdo,
    ) {
    }

    public function create(NewMailingList $mailingList): Result
    {
        if (! $mailingList->isValid) {
            return new Result(
                success: false,
                errors: $mailingList->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createMailingListInPdo->create(
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
}
