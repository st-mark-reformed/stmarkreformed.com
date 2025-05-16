<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Calendar\Month\MonthDayFactory;
use Config\SystemTimezone;
use DateInterval;
use Psr\Clock\ClockInterface;
use Redis;
use RxAnte\DateImmutable;

use function array_map;
use function array_merge;
use function array_values;
use function explode;
use function implode;
use function in_array;
use function json_encode;

readonly class GenerateCalendarPages
{
    public const string JOB_HANDLE = 'generate-calendar-pages';

    public const string JOB_NAME = 'Generate Calendar Pages';

    private const string BASE_URI = 'calendar';

    /** @var string[] */
    private array $monthRange;

    public function __construct(
        ClockInterface $clock,
        private Redis $redis,
        private SystemTimezone $systemTimezone,
        private EventRepository $eventRepository,
        private MonthDayFactory $monthDayFactory,
    ) {
        $today = $clock->now()->setTimezone($this->systemTimezone);

        $oneYear      = new DateInterval('P1Y');
        $oneYearAgo   = $today->sub($oneYear);
        $oneYearAhead = $today->add($oneYear);

        $monthRange  = [];
        $currentDate = $oneYearAgo;

        while ($currentDate <= $oneYearAhead) {
            $monthRange[] = $currentDate->format('Y-m');
            $currentDate  = $currentDate->add(new DateInterval('P1M'));
        }

        $this->monthRange = $monthRange;
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    public function generate(): void
    {
        $setKeys = array_values(array_map(
            fn (string $month) => $this->processMonthPage(
                $month,
            ),
            $this->monthRange,
        ));

        $calendarPageKeys = $this->redis->keys(
            'calendar_data:calendar:page:*',
        );

        foreach ($calendarPageKeys as $key) {
            if (in_array($key, $setKeys, true)) {
                continue;
            }

            $this->redis->del($key);
        }
    }

    private function processMonthPage(string $month): string
    {
        $monthDate = DateImmutable::createFromFormat(
            'Y-m-d',
            $month . '-01',
        );

        $eventsForMonthPadded = $this->eventRepository->getEventsForMonthPadded(
            $month,
        );

        $monthDays = $this->monthDayFactory->create(
            $month,
            $eventsForMonthPadded,
        );

        $pageCachePath = implode('/', [
            self::BASE_URI,
            implode(
                '/',
                explode('-', $month),
            ),
        ]);

        $setKey = 'calendar_data:calendar:page:' . $pageCachePath;

        $this->redis->set(
            $setKey,
            json_encode(array_merge(
                [
                    'pagePath' => $pageCachePath,
                    'monthDays' => $monthDays->asScalarArray(),
                    'monthRows' => $monthDays->rows(),
                    'monthString' => $month,
                    'dateHeading' => $monthDate->format('F Y'),
                    'monthEventsList' => $this->eventRepository->getEventsForMonth(
                        $month,
                    )->asScalarArray(),
                ],
            )),
        );

        return $setKey;
    }
}
