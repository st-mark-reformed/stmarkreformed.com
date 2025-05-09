<?php

declare(strict_types=1);

namespace Config;

use DateTimeZone;

class SystemTimezone extends DateTimeZone
{
    public function __construct()
    {
        parent::__construct('US/Central');
    }
}
