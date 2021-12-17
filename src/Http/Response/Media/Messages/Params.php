<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages;

use App\Messages\RetrieveMessages\MessageRetrievalParams;
use DateTimeImmutable;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function array_filter;
use function array_keys;
use function array_map;
use function count;
use function get_class_vars;
use function gettype;
use function http_build_query;
use function in_array;
use function is_array;

use const ARRAY_FILTER_USE_BOTH;
use const ARRAY_FILTER_USE_KEY;

class Params
{
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $query = $request->getQueryParams();

        $by = $query['by'] ?? [];
        $by = is_array($by) ? $by : [];

        $series = $query['series'] ?? [];
        $series = is_array($series) ? $series : [];

        return new self(
            page: (int) ($query['page'] ?? 1),
            by: array_map(
                static fn (string $val) => $val,
                $by,
            ),
            series: array_map(
                static fn (string $val) => $val,
                $series,
            ),
            scriptureReference: (string) ($query['scripture_reference'] ?? ''),
            title: (string) ($query['title'] ?? ''),
            dateRangeStart: (string) ($query['date_range_start'] ?? ''),
            dateRangeEnd: (string) ($query['date_range_end'] ?? ''),
        );
    }

    /**
     * @param string[] $by
     * @param string[] $series
     */
    public function __construct(
        private int $page = 1,
        private array $by = [],
        private array $series = [],
        private string $scriptureReference = '',
        private string $title = '',
        private string $dateRangeStart = '',
        private string $dateRangeEnd = '',
        private int $perPage = 25,
    ) {
    }

    /**
     * @param string[] $dropKeys
     *
     * @codeCoverageIgnore
     */
    public function toQueryString(array $dropKeys = []): string
    {
        $queryString = http_build_query(array_filter(
            $this->toArray(),
            static fn (string $key) => ! in_array(
                $key,
                $dropKeys,
                true,
            ),
            ARRAY_FILTER_USE_KEY,
        ));

        return match ($queryString === '') {
            true => '',
            false => '?' . $queryString,
        };
    }

    /**
     * @return array<array-key, string|int|array>
     */
    public function toArray(): array
    {
        return array_filter(
            $this->toUnfilteredArray(),
            function (
                int | string | array $val,
                string $key
            ): bool {
                return match ($key) {
                    'page' => $val > 1,
                    default => $this->checkValForArray($val),
                };
            },
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * @param int|string|string[] $val
     *
     * @codeCoverageIgnore
     */
    private function checkValForArray(int | string | array $val): bool
    {
        /** @phpstan-ignore-next-line */
        return match (gettype($val)) {
            'string' => $val !== '',
            'integer' => $val > 0,
            /** @phpstan-ignore-next-line */
            'array' => count($val) > 0,
        };
    }

    /**
     * @return array<array-key, string|int|array>
     */
    public function toUnfilteredArray(): array
    {
        $props = get_class_vars(self::class);

        unset($props['perPage']);

        $array = [];

        foreach (array_keys($props) as $prop) {
            /** @phpstan-ignore-next-line */
            $array[$prop] = $this->{$prop};
        }

        return $array;
    }

    /**
     * @codeCoverageIgnore
     */
    public function toMessageRetrievalParams(): MessageRetrievalParams
    {
        try {
            $dateRangeStart = DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $this->dateRangeStart(),
            );

            if (! ($dateRangeStart instanceof DateTimeImmutable)) {
                throw new LogicException();
            }
        } catch (Throwable) {
            $dateRangeStart = null;
        }

        try {
            $dateRangeEnd = DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $this->dateRangeEnd(),
            );

            if (! ($dateRangeEnd instanceof DateTimeImmutable)) {
                throw new LogicException();
            }
        } catch (Throwable) {
            $dateRangeEnd = null;
        }

        return new MessageRetrievalParams(
            limit: $this->perPage(),
            offset: ($this->page() * $this->perPage()) - $this->perPage(),
            by: $this->by(),
            series: $this->series(),
            scriptureReference: $this->scriptureReference(),
            title: $this->title(),
            dateRangeStart: $dateRangeStart,
            dateRangeEnd: $dateRangeEnd,
        );
    }

    public function hasSearchParams(): bool
    {
        return count($this->by()) > 0 ||
            count($this->series()) > 0 ||
            $this->scriptureReference() !== '' ||
            $this->title() !== '' ||
            $this->dateRangeStart() !== '' ||
            $this->dateRangeEnd() !== '';
    }

    public function hasNoSearchParams(): bool
    {
        return ! $this->hasSearchParams();
    }

    public function page(): int
    {
        return $this->page;
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

    public function dateRangeStart(): string
    {
        return $this->dateRangeStart;
    }

    public function dateRangeEnd(): string
    {
        return $this->dateRangeEnd;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }
}
