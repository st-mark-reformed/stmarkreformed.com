<?php

declare(strict_types=1);

namespace App\PastorsPage;

use App\EmptyUuid;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function count;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class PastorsPageItem implements JsonSerializable
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
        public string $heading = '',
        public string $subheading = '',
        public string $body = '',
    ) {
        /** @phpstan-ignore-next-line */
        $this->date = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date->format('Y-m-d\TH:i') . ':00',
            new DateTimeZone('US/Central'),
        );

        if ($slug === null || $slug === '') {
            $slug = CreatePastorsPageSlug::create($this);
        }

        $this->slug = $slug;

        $messages = PastorsPageItemValidation::validate(pastorsPageItem: $this);

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
     *     heading: string,
     *     subheading: string,
     *     body: string,
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
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'body' => $this->body,
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
            heading: $this->heading,
            subheading: $this->subheading,
            body: $this->body,
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
            heading: $this->heading,
            subheading: $this->subheading,
            body: $this->body,
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
            heading: $this->heading,
            subheading: $this->subheading,
            body: $this->body,
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
            heading: $this->heading,
            subheading: $this->subheading,
            body: $this->body,
        );
    }

    public function withHeading(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            heading: $value,
            subheading: $this->subheading,
            body: $this->body,
        );
    }

    public function withSubheading(string $value): self
    {
        return new self(
            id: $this->id,
            isEnabled: $this->isEnabled,
            date: $this->date,
            title: $this->title,
            slug: $this->slug,
            heading: $this->heading,
            subheading: $value,
            body: $this->body,
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
            heading: $this->heading,
            subheading: $this->subheading,
            body: $value,
        );
    }
}
