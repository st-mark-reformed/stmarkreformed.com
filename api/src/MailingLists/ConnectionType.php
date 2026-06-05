<?php

declare(strict_types=1);

namespace App\MailingLists;

use function strtolower;

enum ConnectionType: string
{
    case Ssl  = 'ssl';
    case Tls  = 'tls';
    case None = 'none';

    public static function fromString(string $value): self
    {
        return self::tryFrom(strtolower($value)) ?? self::Ssl;
    }
}
