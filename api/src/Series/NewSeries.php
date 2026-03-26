<?php

declare(strict_types=1);

namespace App\Series;

use function count;

readonly class NewSeries
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $title = '',
        public SeriesSlug $slug = new SeriesSlug(),
    ) {
        $messages = [];

        if ($title === '') {
            $messages[] = 'Title is required';
        }

        if (! $slug->isValid) {
            $messages[] = $slug->validationMessage;
        }

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
