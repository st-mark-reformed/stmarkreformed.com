<?php

declare(strict_types=1);

namespace App\Email\Entities;

use App\Http\Shared\Exceptions\InvalidEmailAddress;
use App\Http\Shared\ValueObjects\EmailAddressNonEmpty;
use App\Http\Shared\ValueObjects\StringValue;

class EmailRecipient
{
    private EmailAddressNonEmpty $emailAddress;

    private StringValue $name;

    /**
     * @throws InvalidEmailAddress
     */
    public function __construct(
        string $emailAddress,
        string $name = '',
    ) {
        $this->emailAddress = EmailAddressNonEmpty::fromString(
            emailAddress: $emailAddress,
        );

        $this->name = StringValue::fromString(
            value: $name === '' ? $emailAddress : $name,
        );
    }

    public function emailAddress(): EmailAddressNonEmpty
    {
        return $this->emailAddress;
    }

    public function name(): StringValue
    {
        return $this->name;
    }
}
