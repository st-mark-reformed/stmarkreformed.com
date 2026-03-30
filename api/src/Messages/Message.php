<?php

declare(strict_types=1);

namespace App\Messages;

use App\EmptyUuid;
use App\Profiles\Profile;
use App\Series\Series;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function base64_decode;
use function count;
use function ord;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Message implements JsonSerializable
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $slug;

    public DateTimeInterface $date;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public bool $isEnabled = true,
        DateTimeInterface $date = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        ),
        public string $title = '',
        string|null $slug = null,
        public string $audioPath = '',
        public Profile $speaker = new Profile(),
        public string $passage = '',
        public Series $series = new Series(),
        public string $description = '',
    ) {
        /** @phpstan-ignore-next-line */
        $this->date = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date->format('Y-m-d\TH:i') . ':00',
            new DateTimeZone('US/Central'),
        );

        if ($slug === null) {
            $slug = CreateMessageSlug::create($this);
        }

        $this->slug = $slug;

        $messages = MessageValidation::validate(message: $this);

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

    public function withDate(DateTimeInterface $value): self
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
        )->withSlug(value: CreateMessageSlug::create($this));
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
        )->withSlug(value: CreateMessageSlug::create($this));
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

    public function audioPathIsFileUpload(): bool
    {
        if ($this->audioPath === '') {
            return false;
        }

        $audioPath = $this->audioPath;

        if (str_starts_with($audioPath, 'data:')) {
            $commaPosition = strpos($audioPath, ',');

            if ($commaPosition === false) {
                return false;
            }

            $audioPath = substr($audioPath, $commaPosition + 1);
        }

        $decoded = base64_decode($audioPath, true);

        return $decoded !== false && $decoded !== '';
    }

    public function audioPathIsValidFileUpload(): bool
    {
        if ($this->audioPath === '') {
            return false;
        }

        $audioPath = $this->audioPath;

        if (str_starts_with($audioPath, 'data:')) {
            $commaPosition = strpos($audioPath, ',');

            if ($commaPosition === false) {
                return false;
            }

            $audioPath = substr($audioPath, $commaPosition + 1);
        }

        $decoded = base64_decode($audioPath, true);

        if ($decoded === false || $decoded === '') {
            return false;
        }

        if (str_starts_with($decoded, 'ID3')) {
            return true;
        }

        if (strlen($decoded) < 2) {
            return false;
        }

        return ord($decoded[0]) === 0xFF
            && (ord($decoded[1]) & 0xE0) === 0xE0;
    }

    public function createAudioFileNameForPersistence(): string
    {
        if (! $this->audioPathIsFileUpload()) {
            return $this->audioPath;
        }

        return $this->getAudioFileName();
    }

    public function getAudioFileName(): string
    {
        return $this->slug . '.mp3';
    }
}
