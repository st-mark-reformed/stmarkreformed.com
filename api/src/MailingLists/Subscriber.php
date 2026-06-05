<?php

declare(strict_types=1);

namespace App\MailingLists;

use App\EmptyUuid;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

readonly class Subscriber implements JsonSerializable
{
    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public string $name = '',
        public string $emailAddress = '',
    ) {
    }

    /** @return array{id: string, name: string, emailAddress: string} */
    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'emailAddress' => $this->emailAddress,
        ];
    }

    /** @return array{id: string, name: string, emailAddress: string} */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}
