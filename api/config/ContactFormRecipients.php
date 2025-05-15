<?php

declare(strict_types=1);

namespace Config;

use Symfony\Component\Mime\Address;

use function array_filter;
use function array_map;
use function array_values;
use function explode;

readonly class ContactFormRecipients
{
    public static function fromEnvString(string $envString): self
    {
        $addresses = array_filter(
            explode(',', $envString),
            static fn (string $a) => $a !== '',
        );

        return new self(array_values(array_map(
            static fn (string $a) => new Address($a),
            $addresses,
        )));
    }

    /** @param Address[] $recipients */
    public function __construct(public array $recipients)
    {
        array_map(
            static fn (Address $a) => $a,
            $this->recipients,
        );
    }
}
