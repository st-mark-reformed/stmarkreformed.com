<?php

declare(strict_types=1);

namespace App\Messages;

use App\Profiles\Profile;
use App\Series\Series;
use DateTimeImmutable;

use function count;

readonly class Message
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $slug;

    public function __construct(
        public bool $isEnabled = true,
        public DateTimeImmutable $date = new DateTimeImmutable(),
        public string $title = '',
        string|null $slug = null,
        public string $audioPath = '',
        public Profile $speaker = new Profile(),
        public string $passage = '',
        public Series $series = new Series(),
        public string $description = '',
    ) {
        if ($slug === null) {
            $slug = CreateMessageSlug::create($this);
        }

        $this->slug = $slug;

        $messages = MessageValidation::validate(message: $this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
