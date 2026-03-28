<?php

declare(strict_types=1);

namespace App\Messages;

use App\EmptyUuid;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewMessage
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $slug;

    public function __construct(
        public bool $isEnabled = true,
        public DateTimeImmutable $date = new DateTimeImmutable(),
        public string $title = '',
        public string $audioPath = '',
        public UuidInterface $speakerId = new EmptyUuid(),
        public string $passage = '',
        public UuidInterface $seriesId = new EmptyUuid(),
        public string $description = '',
    ) {
        $this->slug = CreateMessageSlug::create($this);

        $messages = MessageValidation::validate(message: $this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
