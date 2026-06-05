<?php

declare(strict_types=1);

namespace App\MailingLists\Schedule;

use App\MailingLists\Check\CheckMailingLists;

readonly class CheckMailingListsJob
{
    public const string JOB_HANDLE = 'check-mailing-lists';

    public const string JOB_NAME = 'Check Mailing Lists';

    public function __construct(private CheckMailingLists $checkMailingLists)
    {
    }

    public function check(): void
    {
        ($this->checkMailingLists)();
    }
}
