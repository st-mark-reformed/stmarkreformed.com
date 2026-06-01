<?php

declare(strict_types=1);

namespace App\InternalMessages;

use App\EmptyUuid;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewInternalMessage
{
    use AudioValidationEntityTrait;

    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $slug;

    public DateTimeInterface $date;

    public function __construct(
        public bool $isEnabled = true,
        DateTimeInterface $date = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        ),
        public string $title = '',
        public string $audioPath = '',
        public int $audioFileSize = 0,
        public UuidInterface $speakerId = new EmptyUuid(),
        public string $passage = '',
        public UuidInterface $seriesId = new EmptyUuid(),
        public string $description = '',
        // Normally leave this empty, this is here for importing from CraftCMS
        public UuidInterface $id = new EmptyUuid(),
        // Normally leave null to derive from date + title. Provided when
        // importing from CraftCMS so the existing audio sub-folder resolves.
        string|null $slug = null,
    ) {
        /** @phpstan-ignore-next-line */
        $this->date = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date->format('Y-m-d\TH:i') . ':00',
            new DateTimeZone('US/Central'),
        );

        if ($slug === null || $slug === '') {
            $slug = CreateInternalMessageSlug::create($this);
        }

        $this->slug = $slug;

        $messages = InternalMessageValidation::validate(message: $this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
