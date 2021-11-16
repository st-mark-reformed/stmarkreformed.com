<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use craft\elements\Entry;

class SetFromMessageFactory
{
    public function __construct(
        private SetToNull $setToNull,
        private SetFromMessage $setFromMessage,
    ) {
    }

    public function make(?Entry $message): SetFromMessageContract
    {
        if ($message === null) {
            return $this->setToNull;
        }

        return $this->setFromMessage;
    }
}
