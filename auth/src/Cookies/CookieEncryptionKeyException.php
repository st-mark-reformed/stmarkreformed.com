<?php

declare(strict_types=1);

namespace App\Cookies;

use App\ExceptionHandling\KnownHandleableError;
use Throwable;

use const SODIUM_CRYPTO_SECRETBOX_KEYBYTES;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix

class CookieEncryptionKeyException extends KnownHandleableError
{
    public function __construct(
        string $message = 'Encryption key must be provided and must be be exactly ' .
        SODIUM_CRYPTO_SECRETBOX_KEYBYTES .
        ' characters in length.',
        int $code = 500,
        Throwable|null $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
