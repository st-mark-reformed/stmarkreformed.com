<?php

declare(strict_types=1);

namespace App\Messages\Message;

use Assert\Assertion;
use Throwable;

readonly class Title
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $title)
    {
        $errorMessage = '';

        try {
            Assertion::notEmpty(
                $title,
                'A Title is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
