<?php

declare(strict_types=1);

namespace App\MailingLists;

use App\EmptyUuid;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class MailingList implements JsonSerializable
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public string $listName = '',
        public string $listAddress = '',
        public string $imapServer = '',
        public int $imapPort = 993,
        public ConnectionType $connectionType = ConnectionType::Ssl,
        public string $username = '',
        public string $password = '',
        public Subscribers $subscribers = new Subscribers(),
    ) {
        $messages = MailingListValidation::validate(mailingList: $this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArrayWithoutPassword();
    }

    /**
     * @return array{
     *     id: string,
     *     listName: string,
     *     listAddress: string,
     *     imapServer: string,
     *     imapPort: int,
     *     connectionType: string,
     *     username: string,
     *     password: string,
     *     subscribers: array<array-key, array{id: string, name: string, emailAddress: string}>,
     * }
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'listName' => $this->listName,
            'listAddress' => $this->listAddress,
            'imapServer' => $this->imapServer,
            'imapPort' => $this->imapPort,
            'connectionType' => $this->connectionType->value,
            'username' => $this->username,
            'password' => $this->password,
            'subscribers' => $this->subscribers->asArray(),
        ];
    }

    /**
     * The list-facing representation that never exposes the IMAP password.
     *
     * @return array{
     *     id: string,
     *     listName: string,
     *     listAddress: string,
     *     imapServer: string,
     *     imapPort: int,
     *     connectionType: string,
     *     username: string,
     *     subscribers: array<array-key, array{id: string, name: string, emailAddress: string}>,
     * }
     */
    public function asArrayWithoutPassword(): array
    {
        $array = $this->asArray();

        unset($array['password']);

        return $array;
    }

    public function withListName(string $value): self
    {
        return new self(
            id: $this->id,
            listName: $value,
            listAddress: $this->listAddress,
            imapServer: $this->imapServer,
            imapPort: $this->imapPort,
            connectionType: $this->connectionType,
            username: $this->username,
            password: $this->password,
            subscribers: $this->subscribers,
        );
    }

    public function withListAddress(string $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $value,
            imapServer: $this->imapServer,
            imapPort: $this->imapPort,
            connectionType: $this->connectionType,
            username: $this->username,
            password: $this->password,
            subscribers: $this->subscribers,
        );
    }

    public function withImapServer(string $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $this->listAddress,
            imapServer: $value,
            imapPort: $this->imapPort,
            connectionType: $this->connectionType,
            username: $this->username,
            password: $this->password,
            subscribers: $this->subscribers,
        );
    }

    public function withImapPort(int $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $this->listAddress,
            imapServer: $this->imapServer,
            imapPort: $value,
            connectionType: $this->connectionType,
            username: $this->username,
            password: $this->password,
            subscribers: $this->subscribers,
        );
    }

    public function withConnectionType(ConnectionType $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $this->listAddress,
            imapServer: $this->imapServer,
            imapPort: $this->imapPort,
            connectionType: $value,
            username: $this->username,
            password: $this->password,
            subscribers: $this->subscribers,
        );
    }

    public function withUsername(string $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $this->listAddress,
            imapServer: $this->imapServer,
            imapPort: $this->imapPort,
            connectionType: $this->connectionType,
            username: $value,
            password: $this->password,
            subscribers: $this->subscribers,
        );
    }

    public function withPassword(string $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $this->listAddress,
            imapServer: $this->imapServer,
            imapPort: $this->imapPort,
            connectionType: $this->connectionType,
            username: $this->username,
            password: $value,
            subscribers: $this->subscribers,
        );
    }

    public function withSubscribers(Subscribers $value): self
    {
        return new self(
            id: $this->id,
            listName: $this->listName,
            listAddress: $this->listAddress,
            imapServer: $this->imapServer,
            imapPort: $this->imapPort,
            connectionType: $this->connectionType,
            username: $this->username,
            password: $this->password,
            subscribers: $value,
        );
    }
}
