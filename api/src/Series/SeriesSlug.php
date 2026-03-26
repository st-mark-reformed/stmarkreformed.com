<?php

declare(strict_types=1);

namespace App\Series;

use Stringable;

use function preg_match;

readonly class SeriesSlug implements Stringable
{
    public bool $isValid;

    public string $validationMessage;

    public function __construct(public string $slug = '')
    {
        if ($slug === '') {
            $this->isValid = false;

            $this->validationMessage = 'Slug is required';

            return;
        }

        if (
            (bool) preg_match(
                '/^[a-z0-9]+(?:-+[a-z0-9]+)*$/',
                $slug,
            )
        ) {
            $this->isValid = true;

            $this->validationMessage = '';

            return;
        }

        $this->isValid = false;

        $this->validationMessage = 'Slug must be URL safe';
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->slug;
    }
}
