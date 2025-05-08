<?php

declare(strict_types=1);

use Solspace\Calendar\Calendar;
use Solspace\Calendar\Services\CalendarsService;

return [
    CalendarsService::class => static function (): CalendarsService {
        return Calendar::getInstance()->calendars;
    },
];
