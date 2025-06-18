<?php

declare(strict_types=1);

namespace App\Messages\Message;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Profiles\Profile\Profile;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

use function array_merge;

readonly class Message
{
    use Cloneable;

    public bool $isValid;

    /** @var string[] */
    public array $errorMessages;

    public UuidInterface $id;

    public function __construct(
        public bool $isPublished,
        public DateTimeImmutable|null $date,
        public Title $title,
        public string $text,
        public Profile|null $speaker,
        public MessageSeries|null $series,
        public AudioFileName $audioFileName,
        UuidInterface|null $id = null,
    ) {
        if ($id === null) {
            $this->id = Uuid::uuid6();
        } else {
            $this->id = $id;
        }

        if (! $isPublished) {
            $this->isValid = true;

            $this->errorMessages = [];

            return;
        }

        $isValid = true;

        $errorMessages = [];

        if ($date === null) {
            $isValid         = false;
            $errorMessages[] = 'A date is required.';
        }

        if (! $title->isValid) {
            $isValid         = false;
            $errorMessages[] = $title->errorMessage;
        }

        if (! $audioFileName->isValid) {
            $isValid         = false;
            $errorMessages[] = $audioFileName->errorMessage;
        }

        if ($speaker === null) {
            $isValid         = false;
            $errorMessages[] = 'A speaker is required.';
        }

        $this->isValid = $isValid;

        $this->errorMessages = $errorMessages;
    }

    /** @return scalar[]|null[]|Array<array-key, Array<scalar>> */
    public function asScalar(): array
    {
        return [
            'id' => $this->id->toString(),
            'isPublished' => $this->isPublished,
            'date' => $this->date?->format('Y-m-d H:i:s'),
            'dateDisplay' => $this->date?->format('F j, Y'),
            'title' => $this->title->title,
            'text' => $this->text,
            'speaker' => $this->speaker?->asScalar(),
            'series' => $this->series?->asScalar(),
            'audioFileName' => $this->audioFileName->audioFileName,
        ];
    }

    public function withId(UuidInterface $id): Message
    {
        return $this->with(id: $id);
    }

    public function withIdFromString(string $id): Message
    {
        return $this->withId(Uuid::fromString($id));
    }

    public function withErrorMessage(string $message): Message
    {
        return $this->with(isValid: false)->with(errorMessages: array_merge(
            $this->errorMessages,
            [$message],
        ));
    }
}
