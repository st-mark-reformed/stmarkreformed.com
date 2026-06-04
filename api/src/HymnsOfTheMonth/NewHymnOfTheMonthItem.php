<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use App\EmptyUuid;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewHymnOfTheMonthItem
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $title;

    public string $slug;

    public DateTimeInterface $date;

    public function __construct(
        public bool $isEnabled = true,
        DateTimeInterface $date = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        ),
        public string $hymnPsalmName = '',
        public string $musicSheetPath = '',
        public HymnPracticeTracks $practiceTracks = new HymnPracticeTracks(),
        string|null $slug = null,
        // Normally leave this empty, this is here for importing from CraftCMS
        public UuidInterface $id = new EmptyUuid(),
    ) {
        // The model tracks a month, not a day. Normalize whatever date we are
        // given to the first of its month at midnight US/Central.
        /** @phpstan-ignore-next-line */
        $this->date = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $date->format('Y-m') . '-01 00:00:00',
            new DateTimeZone('US/Central'),
        );

        $this->title = $this->date->format('F, Y');

        if ($slug === null || $slug === '') {
            $slug = CreateHymnOfTheMonthSlug::create($this);
        }

        $this->slug = $slug;

        $messages = HymnOfTheMonthItemValidation::validate(
            hymnOfTheMonthItem: $this,
        );

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
