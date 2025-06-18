<?php

declare(strict_types=1);

namespace App\Messages\Message;

use Assert\Assertion;
use Throwable;

readonly class AudioFileName
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $audioFileName)
    {
        $errorMessage = '';

        try {
            Assertion::notEmpty(
                $audioFileName,
                'An audio file is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
