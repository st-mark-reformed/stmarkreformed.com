<?php

declare(strict_types=1);

namespace App\Email\Entities;

use App\Http\Shared\ValueObjects\StringValue;
use App\Http\Shared\ValueObjects\StringValueNonEmpty;
use Throwable;

use function count;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Email
{
    /** @var array<string, string> */
    private array $errorMessages = [];

    private StringValueNonEmpty $subject;

    private StringValue $plaintext;

    private StringValue $html;

    public function __construct(
        string $subject,
        private EmailRecipientCollection $recipients,
        private ?EmailRecipient $from,
        string $plaintext = '',
        string $html = '',
    ) {
        try {
            $this->subject = StringValueNonEmpty::fromString(value: $subject);
        } catch (Throwable $e) {
            $this->errorMessages['subject'] = $e->getMessage();
        }

        if ($this->recipients->hasNoRecipients()) {
            $this->errorMessages['recipients'] = 'At least one recipient ' .
                'is required';
        }

        $this->plaintext = StringValue::fromString(value: $plaintext);

        $this->html = StringValue::fromString(value: $html);

        if ($this->plaintext->hasValue() || $this->html->hasValue()) {
            return;
        }

        $this->errorMessages['content'] = '$html or $plaintext is required';
    }

    public function isValid(): bool
    {
        return count($this->errorMessages) < 1;
    }

    public function isNotValid(): bool
    {
        return ! $this->isValid();
    }

    /**
     * @return array<string, string>
     */
    public function errorMessages(): array
    {
        return $this->errorMessages;
    }

    public function subject(): StringValueNonEmpty
    {
        return $this->subject;
    }

    public function recipients(): EmailRecipientCollection
    {
        return $this->recipients;
    }

    public function from(): ?EmailRecipient
    {
        return $this->from;
    }

    public function plaintext(): StringValue
    {
        return $this->plaintext;
    }

    public function html(): StringValue
    {
        return $this->html;
    }
}
