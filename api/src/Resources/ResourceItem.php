<?php

declare(strict_types=1);

namespace App\Resources;

use App\EmptyUuid;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class ResourceItem implements JsonSerializable
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
        public string $body = '',
        public ResourceDownloads $downloads = new ResourceDownloads(),
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
     *     body: string,
     *     downloads: array<array-key, array{filename: string}>,
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
            'body' => $this->body,
            'downloads' => $this->downloads->asArray(),
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
            body: $this->body,
            downloads: $this->downloads,
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
            body: $this->body,
            downloads: $this->downloads,
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
            body: $this->body,
            downloads: $this->downloads,
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
            body: $this->body,
            downloads: $this->downloads,
        );
    }

    public function withBody(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            body: $value,
            downloads: $this->downloads,
        );
    }

    public function withDownloads(ResourceDownloads $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            body: $this->body,
            downloads: $value,
        );
    }
}
