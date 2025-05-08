<?php

declare(strict_types=1);

namespace App\Email\Entities;

use function array_map;
use function count;

class EmailRecipientCollection
{
    /** @var EmailRecipient[] */
    private array $recipients = [];

    /**
     * @param EmailRecipient[] $recipients
     */
    public function __construct(iterable $recipients)
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient(recipient: $recipient);
        }
    }

    private function addRecipient(EmailRecipient $recipient): void
    {
        $this->recipients[] = $recipient;
    }

    public function hasRecipients(): bool
    {
        return count($this->recipients) > 0;
    }

    public function hasNoRecipients(): bool
    {
        return ! $this->hasRecipients();
    }

    public function map(callable $callback): mixed
    {
        return array_map(
            $callback,
            $this->recipients,
        );
    }
}
