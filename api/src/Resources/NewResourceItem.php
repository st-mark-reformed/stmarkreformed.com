<?php

declare(strict_types=1);

namespace App\Resources;

use App\EmptyUuid;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewResourceItem
{
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
        string|null $slug = null,
        public string $body = '',
        public ResourceDownloads $downloads = new ResourceDownloads(),
        // Normally leave this empty, this is here for importing from CraftCMS
        public UuidInterface $id = new EmptyUuid(),
    ) {
        /** @phpstan-ignore-next-line */
        $this->date = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date->format('Y-m-d\TH:i') . ':00',
            new DateTimeZone('US/Central'),
        );

        if ($slug === null || $slug === '') {
            $slug = CreateResourceSlug::create($this);
        }

        $this->slug = $slug;

        $messages = ResourceItemValidation::validate(resourceItem: $this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
