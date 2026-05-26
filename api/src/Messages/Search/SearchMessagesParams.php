<?php

declare(strict_types=1);

namespace App\Messages\Search;

use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function array_map;
use function count;
use function is_array;
use function is_int;
use function is_string;
use function max;

readonly class SearchMessagesParams
{
    /**
     * @param string[] $bySpeakerSlugs
     * @param string[] $bySeriesSlugs
     */
    public function __construct(
        public int $page = 1,
        public array $bySpeakerSlugs = [],
        public array $bySeriesSlugs = [],
        public string $scriptureReference = '',
        public string $title = '',
        public string $dateRangeStart = '',
        public string $dateRangeEnd = '',
        public int $perPage = 25,
    ) {
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $query = $request->getQueryParams();

        return new self(
            page: max(1, self::asInt(value: $query['page'] ?? null)),
            bySpeakerSlugs: self::stringArray($query['by'] ?? []),
            bySeriesSlugs: self::stringArray($query['series'] ?? []),
            scriptureReference: self::asString(
                value: $query['scripture_reference'] ?? null,
            ),
            title: self::asString(value: $query['title'] ?? null),
            dateRangeStart: self::asString(
                value: $query['date_range_start'] ?? null,
            ),
            dateRangeEnd: self::asString(
                value: $query['date_range_end'] ?? null,
            ),
        );
    }

    public function hasTextSearch(): bool
    {
        return count($this->bySpeakerSlugs) > 0
            || count($this->bySeriesSlugs) > 0
            || $this->scriptureReference !== ''
            || $this->title !== '';
    }

    public function hasDateRange(): bool
    {
        return $this->dateRangeStart !== '' || $this->dateRangeEnd !== '';
    }

    public function dateRangeStartAsDate(): DateTimeImmutable|null
    {
        return self::parseDate(value: $this->dateRangeStart);
    }

    public function dateRangeEndAsDate(): DateTimeImmutable|null
    {
        return self::parseDate(value: $this->dateRangeEnd);
    }

    /** @return string[] */
    private static function stringArray(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_map(
            static fn (mixed $item): string => is_string($item) ? $item : '',
            $value,
        );
    }

    private static function asString(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }

    private static function asInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return (int) $value;
        }

        return 1;
    }

    private static function parseDate(string $value): DateTimeImmutable|null
    {
        if ($value === '') {
            return null;
        }

        try {
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        } catch (Throwable) {
            return null;
        }

        return $date === false ? null : $date;
    }
}
