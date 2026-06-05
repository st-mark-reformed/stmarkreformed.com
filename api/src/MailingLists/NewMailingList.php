<?php

declare(strict_types=1);

namespace App\MailingLists;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewMailingList
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $listName = '',
        public string $listAddress = '',
        public string $imapServer = '',
        public int $imapPort = 993,
        public ConnectionType $connectionType = ConnectionType::Ssl,
        public string $username = '',
        public string $password = '',
        public Subscribers $subscribers = new Subscribers(),
        // Normally leave this empty; present so a specific id can be supplied.
        public UuidInterface $id = new EmptyUuid(),
    ) {
        $messages = MailingListValidation::validate(mailingList: $this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
