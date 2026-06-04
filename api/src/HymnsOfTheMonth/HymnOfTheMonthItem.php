<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use App\EmptyUuid;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class HymnOfTheMonthItem implements JsonSerializable
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $title;

    public string $slug;

    public DateTimeInterface $date;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public bool $isEnabled = true,
        DateTimeInterface $date = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        ),
        public string $hymnPsalmName = '',
        public string $musicSheetPath = '',
        public HymnPracticeTracks $practiceTracks = new HymnPracticeTracks(),
        string|null $slug = null,
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

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    /**
     * @return array{
     *     id: string,
     *     isEnabled: bool,
     *     date: string,
     *     title: string,
     *     slug: string,
     *     hymnPsalmName: string,
     *     musicSheetPath: string,
     *     practiceTracks: array<array-key, array{title: string, path: string}>,
     * }
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'isEnabled' => $this->isEnabled,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'title' => $this->title,
            'slug' => $this->slug,
            'hymnPsalmName' => $this->hymnPsalmName,
            'musicSheetPath' => $this->musicSheetPath,
            'practiceTracks' => $this->practiceTracks->asArray(),
        ];
    }

    public function withEnabled(bool $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $value,
            date: $this->date,
            hymnPsalmName: $this->hymnPsalmName,
            musicSheetPath: $this->musicSheetPath,
            practiceTracks: $this->practiceTracks,
            slug: $this->slug,
        );
    }

    public function withDate(DateTimeInterface $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $value,
            hymnPsalmName: $this->hymnPsalmName,
            musicSheetPath: $this->musicSheetPath,
            practiceTracks: $this->practiceTracks,
            // Date drives the slug, so let it re-derive from the new date.
            slug: null,
        );
    }

    public function withHymnPsalmName(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            hymnPsalmName: $value,
            musicSheetPath: $this->musicSheetPath,
            practiceTracks: $this->practiceTracks,
            slug: $this->slug,
        );
    }

    public function withMusicSheetPath(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            hymnPsalmName: $this->hymnPsalmName,
            musicSheetPath: $value,
            practiceTracks: $this->practiceTracks,
            slug: $this->slug,
        );
    }

    public function withPracticeTracks(HymnPracticeTracks $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            hymnPsalmName: $this->hymnPsalmName,
            musicSheetPath: $this->musicSheetPath,
            practiceTracks: $value,
            slug: $this->slug,
        );
    }
}
