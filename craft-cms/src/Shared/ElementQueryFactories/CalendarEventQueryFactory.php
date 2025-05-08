<?php

declare(strict_types=1);

namespace App\Shared\ElementQueryFactories;

use Solspace\Calendar\Elements\Db\EventQuery;
use Solspace\Calendar\Elements\Event;

class CalendarEventQueryFactory
{
    /**
     * @param array<array-key, scalar>|null $attributes
     *
     * @phpstan-ignore-next-line
     */
    public function make(?array $attributes = null): EventQuery
    {
        /** @phpstan-ignore-next-line */
        return Event::buildQuery($attributes);
    }
}
