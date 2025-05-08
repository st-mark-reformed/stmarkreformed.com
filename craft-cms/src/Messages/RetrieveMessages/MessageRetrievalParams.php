<?php

declare(strict_types=1);

namespace App\Messages\RetrieveMessages;

use DateTimeInterface;

use function count;

class MessageRetrievalParams
{
    /**
     * @param string[] $by
     * @param string[] $series
     */
    public function __construct(
        private int $limit = 25,
        private int $offset = 0,
        private array $by = [],
        private array $series = [],
        private string $scriptureReference = '',
        private string $title = '',
        private ?DateTimeInterface $dateRangeStart = null,
        private ?DateTimeInterface $dateRangeEnd = null,
    ) {
    }

    public function hasSearch(): bool
    {
        return count($this->by()) > 0 ||
            count($this->series()) > 0 ||
            $this->scriptureReference() !== '' ||
            $this->title() !== '' ||
            $this->dateRangeStart() !== null ||
            $this->dateRangeEnd() !== null;
    }

    public function hasNoSearch(): bool
    {
        return ! $this->hasSearch();
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    /**
     * @return string[]
     */
    public function by(): array
    {
        return $this->by;
    }

    /**
     * @return string[]
     */
    public function series(): array
    {
        return $this->series;
    }

    public function scriptureReference(): string
    {
        return $this->scriptureReference;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function dateRangeStart(): ?DateTimeInterface
    {
        return $this->dateRangeStart;
    }

    public function dateRangeEnd(): ?DateTimeInterface
    {
        return $this->dateRangeEnd;
    }
}
