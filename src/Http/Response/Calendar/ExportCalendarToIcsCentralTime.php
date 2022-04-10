<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use Carbon\Carbon;
use DateTimeZone;
use Solspace\Calendar\Elements\Event;
use Solspace\Calendar\Library\DateHelper;
use Solspace\Calendar\Library\Export\AbstractExportCalendar;

use function explode;
use function implode;
use function md5;
use function sprintf;
use function strip_tags;

/**
 * Based on @see \Solspace\Calendar\Library\Export\ExportCalendarToIcs
 */
class ExportCalendarToIcsCentralTime extends AbstractExportCalendar
{
    private const FORMAT_STRING = 'Y-m-d-G-i-s';

    private Carbon $now;

    /**
     * Collect events and parse them, and build a string
     * That will be exported to a file.
     */
    protected function prepareStringForExport(): string
    {
        $events = $this->getEventQuery()->all();

        $exportString  = "BEGIN:VCALENDAR\r\n";
        $exportString .= "PRODID:-//Solspace/Calendar//EN\r\n";
        $exportString .= "VERSION:2.0\r\n";
        $exportString .= "CALSCALE:GREGORIAN\r\n";

        $this->now = Carbon::now(DateHelper::UTC);

        foreach ($events as $event) {
            $startDate = Carbon::createFromFormat(
                self::FORMAT_STRING,
                /** @phpstan-ignore-next-line */
                $event->getStartDate()->format(self::FORMAT_STRING),
                new DateTimeZone('US/Central'),
            );

            /** @phpstan-ignore-next-line */
            $exportString .= $this->combineExportString($event, $startDate);

            /** @phpstan-ignore-next-line */
            if (! $event->getSelectDatesAsDates()) {
                continue;
            }

            foreach ($event->getSelectDatesAsDates() as $date) {
                $date = Carbon::createFromFormat(
                    self::FORMAT_STRING,
                    $date->format(self::FORMAT_STRING),
                    new DateTimeZone('US/Central'),
                );

                /** @phpstan-ignore-next-line */
                $dateCarbon = Carbon::createFromTimestampUTC($date->getTimestamp());
                $dateCarbon->setTime(
                    /** @phpstan-ignore-next-line */
                    $startDate->hour,
                    /** @phpstan-ignore-next-line */
                    $startDate->minute,
                    /** @phpstan-ignore-next-line */
                    $startDate->second
                );

                $exportString .= $this->combineExportString($event, $dateCarbon);
            }
        }

        return $exportString . 'END:VCALENDAR';
    }

    /**
     * Builds a VEVENT string and returns it.
     *
     * @phpstan-ignore-next-line
     */
    private function combineExportString(Event $event, Carbon $date): string
    {
        $eventId      = $event->getId();
        $exportString = '';

        $eStartDate = Carbon::createFromFormat(
            self::FORMAT_STRING,
            /** @phpstan-ignore-next-line */
            $event->getStartDate()->format(self::FORMAT_STRING),
            new DateTimeZone('US/Central'),
        );

        $endDate = Carbon::createFromFormat(
            self::FORMAT_STRING,
            /** @phpstan-ignore-next-line */
            $event->getEndDate()->format(self::FORMAT_STRING),
            new DateTimeZone('US/Central'),
        );

        // $timezone = $this->getOption('timezone', $event->getCalendar()->getIcsTimezone());
        $timezone = 'US/Central';
        /** @phpstan-ignore-next-line */
        $dateDiff = $eStartDate->diff($endDate);

        $startDate = $date->copy();
        $startDate->setTime(
            /** @phpstan-ignore-next-line */
            $eStartDate->hour,
            /** @phpstan-ignore-next-line */
            $eStartDate->minute,
            /** @phpstan-ignore-next-line */
            $eStartDate->second
        );
        $endDate = $startDate->copy()->add($dateDiff);

        $description            = null;
        $descriptionFieldHandle = $event->getCalendar()->descriptionFieldHandle;
        /** @phpstan-ignore-next-line */
        if (isset($event->{$descriptionFieldHandle})) {
            /** @phpstan-ignore-next-line */
            $description = $event->{$descriptionFieldHandle};
        }

        $location            = null;
        $locationFieldHandle = $event->getCalendar()->locationFieldHandle;
        /** @phpstan-ignore-next-line */
        if (isset($event->{$locationFieldHandle})) {
            /** @phpstan-ignore-next-line */
            $location = $event->{$locationFieldHandle};
        }

        $title   = $event->title;
        $uidHash = md5($eventId . $title . $description . $date->timestamp);

        $exportString .= "BEGIN:VEVENT\r\n";
        $exportString .= sprintf("UID:%s@solspace.com\r\n", $uidHash);
        $exportString .= sprintf("DTSTAMP:%s\r\n", $this->now->format(self::DATE_TIME_FORMAT));

        if ($description) {
            $exportString .= sprintf("DESCRIPTION:%s\r\n", $this->prepareString(strip_tags($description)));
        }

        if ($location) {
            $exportString .= sprintf("LOCATION:%s\r\n", $this->prepareString(strip_tags($location)));
        }

        if ($event->isAllDay()) {
            $exportString .= sprintf("DTSTART;VALUE=DATE:%s\r\n", $startDate->format(self::DATE_FORMAT));
            $exportString .= sprintf(
                "DTEND;VALUE=DATE:%s\r\n",
                $endDate->copy()->addDay()->format(self::DATE_FORMAT)
            );
        /** @phpstan-ignore-next-line */
        } elseif ($timezone === 'UTC') {
            $exportString .= sprintf("DTSTART:%sZ\r\n", $startDate->format(self::DATE_TIME_FORMAT));
            $exportString .= sprintf("DTEND:%sZ\r\n", $endDate->format(self::DATE_TIME_FORMAT));
        /** @phpstan-ignore-next-line */
        } elseif ($timezone === DateHelper::FLOATING_TIMEZONE) {
            $exportString .= sprintf("DTSTART:%s\r\n", $startDate->format(self::DATE_TIME_FORMAT));
            $exportString .= sprintf("DTEND:%s\r\n", $endDate->format(self::DATE_TIME_FORMAT));
        } else {
            $exportString .= sprintf("DTSTART;TZID=%s:%s\r\n", $timezone, $startDate->format(self::DATE_TIME_FORMAT));
            $exportString .= sprintf("DTEND;TZID=%s:%s\r\n", $timezone, $endDate->format(self::DATE_TIME_FORMAT));
        }

        $selectDates = $event->getSelectDates();
        /** @phpstan-ignore-next-line */
        if (empty($selectDates) && $event->isRepeating()) {
            $rrule             = $event->getRRule();
            [$dtstart, $rrule] = explode("\n", $rrule);

            $exportString .= sprintf("%s\r\n", $rrule);

            $exceptionDatesValues = [];
            foreach ($event->getExceptionDateStrings() as $exceptionDate) {
                $exceptionDate = new Carbon($exceptionDate, DateHelper::UTC);
                if ($event->isAllDay()) {
                    $exceptionDatesValues[] = $exceptionDate->format(self::DATE_FORMAT);
                } else {
                    $exceptionDate->setTime($startDate->hour, $startDate->minute, $startDate->second);
                    $exceptionDatesValues[] = $exceptionDate->format(self::DATE_TIME_FORMAT);
                }
            }

            $exceptionDates = implode(',', $exceptionDatesValues);
            /** @phpstan-ignore-next-line */
            if ($exceptionDates) {
                if ($event->isAllDay()) {
                    $exportString .= sprintf("EXDATE;VALUE=DATE:%s\r\n", $exceptionDates);
                } else {
                    $exportString .= sprintf("EXDATE:%s\r\n", $exceptionDates);
                }
            }
        }

        /** @phpstan-ignore-next-line */
        $exportString .= sprintf("SUMMARY:%s\r\n", $this->prepareString($title));

        return $exportString . "END:VEVENT\r\n";
    }
}
