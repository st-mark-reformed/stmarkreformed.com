<?php

declare(strict_types=1);

namespace App\MailingLists\Check;

use App\MailingLists\MailingList;
use App\MailingLists\MailingListsRepository;

/**
 * Entry point for the scheduled mailing-list check: loads every configured list
 * and runs the per-list check against each.
 */
readonly class CheckMailingLists
{
    public function __construct(
        private MailingListsRepository $repository,
        private CheckMailingList $checkMailingList,
    ) {
    }

    public function __invoke(): void
    {
        $mailingLists = $this->repository->findAll();

        if ($mailingLists->count() < 1) {
            return;
        }

        $mailingLists->map(fn (MailingList $mailingList) => ($this->checkMailingList)(
            mailingList: $mailingList,
        ));
    }
}
