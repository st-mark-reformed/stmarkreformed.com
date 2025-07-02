<?php

declare(strict_types=1);

namespace App\Messages\Series\MessageSeries;

use Assert\Assertion;
use Throwable;

readonly class Slug
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $slug)
    {
        $errorMessage = '';

        try {
            Assertion::notEmpty(
                $slug,
                'A Slug is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        if ($slug !== '') {
            try {
                Assertion::regex(
                    $slug,
                    '/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/',
                    'Slug must be alphanumeric with dash separators',
                );
            } catch (Throwable $e) {
                $errorMessage = $e->getMessage();
            }
        }

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
