<?php

declare(strict_types=1);

namespace Config;

use RuntimeException;

use function dirname;
use function file_exists;
use function file_get_contents;
use function is_string;
use function trim;

readonly class SigningCertificate
{
    public function get(): string
    {
        $certPath = dirname(__DIR__) . '/storage/auth/signing-certificate.cert';

        if (! file_exists($certPath)) {
            $msg = 'Could not load ' . $certPath;

            echo $msg;

            throw new RuntimeException($msg);
        }

        $fileContent = file_get_contents($certPath);

        if (! is_string($fileContent)) {
            $msg = 'An unknown error occurred loading ' . $certPath;

            echo $msg;

            throw new RuntimeException($msg);
        }

        return trim($fileContent);
    }
}
