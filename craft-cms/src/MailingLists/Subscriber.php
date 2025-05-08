<?php

declare(strict_types=1);

namespace App\MailingLists;

class Subscriber
{
    public function __construct(
        private string $name,
        private string $emailAddress,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }
}
