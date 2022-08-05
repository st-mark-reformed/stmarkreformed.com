<?php

declare(strict_types=1);

namespace App\MailingLists;

class MailingList
{
    public function __construct(
        private string $listName,
        private string $listAddress,
        private string $userName,
        private string $password,
        private string $server,
        private int $port,
        private string $connectionType,
        private SubscriberCollection $subscribers,
    ) {
    }

    public function listName(): string
    {
        return $this->listName;
    }

    public function listAddress(): string
    {
        return $this->listAddress;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function server(): string
    {
        return $this->server;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function connectionType(): string
    {
        return $this->connectionType;
    }

    public function subscribers(): SubscriberCollection
    {
        return $this->subscribers;
    }
}
