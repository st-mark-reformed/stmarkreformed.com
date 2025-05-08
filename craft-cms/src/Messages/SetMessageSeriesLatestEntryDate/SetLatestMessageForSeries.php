<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate;

use App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry\SetFromMessageFactory;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Category;
use craft\elements\Entry;

use function assert;

class SetLatestMessageForSeries
{
    public function __construct(
        private EntryQueryFactory $entryQueryFactory,
        private SetFromMessageFactory $setFromEntryFactory,
    ) {
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function set(Category $series): void
    {
        $messageQuery = $this->entryQueryFactory->make();

        $messageQuery->section('messages');

        $messageQuery->relatedTo([
            'targetElement' => $series,
            'field' => 'messageSeries',
        ]);

        $message = $messageQuery->one();

        assert($message instanceof Entry || $message === null);

        $this->setFromEntryFactory->make(message: $message)->set(
            series: $series,
            message: $message,
        );
    }
}
