<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use Solspace\Calendar\Elements\Event;

class MonthEvent
{
    /**
     * @phpstan-ignore-next-line
     */
    public function __construct(
        /** @phpstan-ignore-next-line */
        public Event $event,
        public bool $isInPast,
    ) {
    }
}
