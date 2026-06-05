<?php

declare(strict_types=1);

namespace App\MailingLists;

readonly class MailingListResult
{
    public bool $hasMailingList;

    public MailingList $mailingList;

    public function __construct(MailingList|null $mailingList = null)
    {
        $this->hasMailingList = $mailingList !== null;
        $this->mailingList    = $mailingList ?? new MailingList();
    }
}
