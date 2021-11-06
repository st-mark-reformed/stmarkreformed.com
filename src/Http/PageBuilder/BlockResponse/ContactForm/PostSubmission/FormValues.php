<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission;

use App\Http\Shared\ValueObjects\EmailAddressNonEmpty;
use App\Http\Shared\ValueObjects\StringValue;
use App\Http\Shared\ValueObjects\StringValueNonEmpty;
use Throwable;

use function count;

/**
 * @psalm-suppress RedundantPropertyInitializationCheck
 */
class FormValues
{
    /** @var array<string, string> */
    private array $errorMessages = [];

    /** @psalm-suppress PropertyNotSetInConstructor */
    private StringValue $fromUrl;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private StringValue $redirectUrl;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private StringValueNonEmpty $name;

    private string $nameRaw;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private EmailAddressNonEmpty $email;

    private string $emailRaw;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private StringValueNonEmpty $message;

    private string $messageRaw;

    public function __construct(
        string $fromUrl,
        string $redirectUrl,
        string $name,
        string $email,
        string $message,
    ) {
        $this->fromUrl = new StringValue(value: $fromUrl);

        $this->redirectUrl = new StringValue(value: $redirectUrl);

        $this->nameRaw = $name;

        try {
            $this->name = StringValueNonEmpty::fromString(value: $name);
        } catch (Throwable $e) {
            $this->errorMessages['your_name'] = $e->getMessage();
        }

        $this->emailRaw = $email;

        try {
            $this->email = EmailAddressNonEmpty::fromString(
                emailAddress: $email,
            );
        } catch (Throwable $e) {
            $this->errorMessages['your_email'] = $e->getMessage();
        }

        $this->messageRaw = $message;

        try {
            $this->message = StringValueNonEmpty::fromString(value: $message);
        } catch (Throwable $e) {
            $this->errorMessages['message'] = $e->getMessage();
        }
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

    public function withErrorMessage(string $key, string $message): self
    {
        $clone = clone $this;

        $clone->errorMessages[$key] = $message;

        return $clone;
    }

    /**
     * @return string[]
     */
    public function formattedErrorMessages(): array
    {
        $nameMap = [
            'your_name' => 'Your Name: ',
            'your_email' => 'Your Email Address: ',
            'message' => 'Message: ',
        ];

        $messageList = [];

        foreach ($this->errorMessages as $key => $val) {
            $key = $nameMap[$key] ?? '';

            $messageList[] = $key . $val;
        }

        return $messageList;
    }

    public function fromUrl(): StringValue
    {
        return $this->fromUrl;
    }

    public function redirectUrl(): StringValue
    {
        return $this->redirectUrl;
    }

    public function name(): StringValueNonEmpty
    {
        return $this->name;
    }

    public function nameRaw(): string
    {
        return $this->nameRaw;
    }

    public function email(): EmailAddressNonEmpty
    {
        return $this->email;
    }

    public function emailRaw(): string
    {
        return $this->emailRaw;
    }

    public function message(): StringValueNonEmpty
    {
        return $this->message;
    }

    public function messageRaw(): string
    {
        return $this->messageRaw;
    }
}
