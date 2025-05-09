<?php

declare(strict_types=1);

namespace App\Calendar;

use DateTimeImmutable;
use RxAnte\DateImmutable;
use Spatie\Cloneable\Cloneable;

use function max;

readonly class Event
{
    use Cloneable;

    public bool $isMultiDay;

    public bool $isAllDay;

    public int $totalDays;

    public function __construct(
        public string $uid,
        public string $summary,
        public string $description,
        public string $location,
        public bool $isInPast,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
    ) {
        $startDay = DateImmutable::createFromFormat(
            'Y-m-d 00:00:00',
            $startDate->format('Y-m-d 00:00:00'),
        );

        $endDay = DateImmutable::createFromFormat(
            'Y-m-d 00:00:00',
            $endDate->format('Y-m-d 00:00:00'),
        );

        $totalDaysPreProcess = (int) $endDay->diff(
            $startDay,
        )->format('%a');

        $this->totalDays = max(1, $totalDaysPreProcess);

        $this->isMultiDay = $this->totalDays > 1;

        if ($totalDaysPreProcess > 0) {
            $this->isAllDay = true;
        } else {
            $this->isAllDay = false;
        }
    }

    public function withStartDate(DateTimeImmutable|DateImmutable $date): Event
    {
        return $this->with(startDate: $date);
    }

    /** @return mixed[] */
    public function asScalarArray(): array
    {
        return [
            'uid' => $this->uid,
            'summary' => $this->summary,
            'description' => $this->description,
            'location' => $this->location,
            'isInPast' => $this->isInPast,
            'startDate' => $this->startDate->format('Y-m-d H:i:s'),
            'endDate' => $this->endDate->format('Y-m-d H:i:s'),
            'isMultiDay' => $this->isMultiDay,
            'isAllDay' => $this->isAllDay,
            'totalDays' => $this->totalDays,
        ];
    }
}
