<?php

declare(strict_types=1);

namespace App\Calendar\Month;

use Config\SystemTimezone;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;

use function assert;

readonly class MonthRangeFactory
{
    public function __construct(private SystemTimezone $systemTimezone)
    {
    }

    /**
     * @param string $month as 2022-01, 2022-04 etc.
     *
     * @return DatePeriod<DateTimeImmutable>
     */
    public function make(string $month): DatePeriod
    {
        $firstDay = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $month . '-01 00:00:00',
            $this->systemTimezone,
        );

        assert($firstDay instanceof DateTimeImmutable);

        $rangeFirstDay = $this->getRangeFirstDay($firstDay);

        $daysInMonth = $firstDay->format('t');

        $lastDay = $firstDay->add(
            new DateInterval('P' . (((int) $daysInMonth) - 1) . 'D'),
        );

        $lastDay = $lastDay->add(new DateInterval('PT2H'));

        $rangeLastDay = $this->getRangeLastDay($lastDay);

        return new DatePeriod(
            $rangeFirstDay,
            new DateInterval('P1D'),
            $rangeLastDay,
        );
    }

    private function getRangeFirstDay(DateTimeImmutable $day): DateTimeImmutable
    {
        return match ($day->format('D')) {
            'Mon' => $day->sub(new DateInterval('P1D')),
            'Tue' => $day->sub(new DateInterval('P2D')),
            'Wed' => $day->sub(new DateInterval('P3D')),
            'Thu' => $day->sub(new DateInterval('P4D')),
            'Fri' => $day->sub(new DateInterval('P5D')),
            'Sat' => $day->sub(new DateInterval('P6D')),
            default => $day,
        };
    }

    private function getRangeLastDay(DateTimeImmutable $day): DateTimeImmutable
    {
        return match ($day->format('D')) {
            'Sun' => $day->add(new DateInterval('P6D')),
            'Mon' => $day->add(new DateInterval('P5D')),
            'Tue' => $day->add(new DateInterval('P4D')),
            'Wed' => $day->add(new DateInterval('P3D')),
            'Thu' => $day->add(new DateInterval('P2D')),
            'Fri' => $day->add(new DateInterval('P1D')),
            default => $day,
        };
    }
}
