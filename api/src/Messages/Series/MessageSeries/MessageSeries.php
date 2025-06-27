<?php

declare(strict_types=1);

namespace App\Messages\Series\MessageSeries;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

use function array_merge;

readonly class MessageSeries
{
    use Cloneable;

    public bool $isValid;

    /** @var string[] */
    public array $errorMessages;

    public UuidInterface $id;

    public function __construct(
        public Title $title,
        public Slug $slug,
        UuidInterface|null $id = null,
    ) {
        if ($id === null) {
            $this->id = Uuid::uuid6();
        } else {
            $this->id = $id;
        }

        $isValid = true;

        $errorMessages = [];

        if (! $title->isValid) {
            $isValid         = false;
            $errorMessages[] = $title->errorMessage;
        }

        if (! $slug->isValid) {
            $isValid         = false;
            $errorMessages[] = $slug->errorMessage;
        }

        $this->isValid = $isValid;

        $this->errorMessages = $errorMessages;
    }

    /** @return scalar[] */
    public function asScalar(): array
    {
        return [
            'id' => $this->id->toString(),
            'title' => $this->title->title,
            'slug' => $this->slug->slug,
        ];
    }

    public function withId(UuidInterface $id): MessageSeries
    {
        return $this->with(id: $id);
    }

    public function withIdFromString(string $id): MessageSeries
    {
        return $this->withId(Uuid::fromString($id));
    }

    public function withErrorMessage(string $message): MessageSeries
    {
        return $this->with(isValid: false)->with(errorMessages: array_merge(
            $this->errorMessages,
            [$message],
        ));
    }
}
