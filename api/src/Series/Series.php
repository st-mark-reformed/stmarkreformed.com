<?php

declare(strict_types=1);

namespace App\Series;

use App\EmptyUuid;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function count;
use function is_string;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Series implements JsonSerializable
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public string $title = '',
        public SeriesSlug $slug = new SeriesSlug(),
    ) {
        $messages = SeriesValidation::validate($this);

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
     *     title: string,
     *     slug: string,
     * }
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'title' => $this->title,
            'slug' => $this->slug->toString(),
        ];
    }

    public function withTitle(string $value): self
    {
        return new self(
            id: $this->id,
            title: $value,
            slug: $this->slug,
        );
    }

    public function withSlug(SeriesSlug|string $value): self
    {
        if (is_string($value)) {
            $value = new SeriesSlug($value);
        }

        return new self(
            id: $this->id,
            title: $this->title,
            slug: $value,
        );
    }
}
