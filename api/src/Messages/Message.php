<?php

declare(strict_types=1);

namespace App\Messages;

use App\EmptyUuid;
use App\Profiles\Profile;
use App\Series\Series;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class Message
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $slug;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public bool $isEnabled = true,
        public DateTimeInterface $date = new DateTimeImmutable(),
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

    /**
     * @return array{
     *     isEnabled: bool,
     *     date: string,
     *     title: string,
     *     slug: string,
     *     audioPath: string,
     *     speaker: array{
     *         id: string,
     *         titleOrHonorific: string,
     *         firstName: string,
     *         lastName: string,
     *         fullName: string,
     *         fullNameWithHonorific: string,
     *         email: string,
     *         leadershipPosition: string,
     *         leadershipPositionHumanReadable: string,
     *         bio: string,
     *         hasMessages: bool,
     *     },
     *     passage: string,
     *     series: array{
     *         id: string,
     *         title: string,
     *         slug: string,
     *     },
     *     description: string,
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
            'audioPath' => $this->audioPath,
            'speaker' => $this->speaker->asArray(),
            'passage' => $this->passage,
            'series' => $this->series->asArray(),
            'description' => $this->description,
        ];
    }

    public function withEnabled(bool $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $value,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withSpeaker(Profile $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $value,
            passage: $this->passage,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withDate(DateTimeImmutable $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $value,
            title: $this->title,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withTitle(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $value,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withSlug(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $value,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withAudioPath(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            audioPath: $value,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withPassage(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $value,
            series: $this->series,
            description: $this->description,
        );
    }

    public function withSeries(Series $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $value,
            description: $this->description,
        );
    }

    public function withDescription(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            audioPath: $this->audioPath,
            speaker: $this->speaker,
            passage: $this->passage,
            series: $this->series,
            description: $value,
        );
    }
}
