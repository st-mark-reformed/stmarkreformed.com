<?php

declare(strict_types=1);

namespace App\Craft\SetMessageEntrySlug;

use App\Craft\SetMessageEntrySlug\Services\DoNotSetSlug;
use App\Craft\SetMessageEntrySlug\Services\SetMessageEntrySlug;
use App\Craft\SetMessageEntrySlug\Services\SetMessageEntrySlugContract;
use craft\elements\Entry;
use craft\events\ModelEvent;
use DateTime;
use Throwable;
use yii\base\InvalidConfigException;

use function assert;
use function in_array;

class SetMessageEntrySlugFactory
{
    public function make(ModelEvent $eventModel): SetMessageEntrySlugContract
    {
        try {
            return $this->innerMake(eventModel: $eventModel);
        } catch (Throwable) {
            return new DoNotSetSlug();
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function innerMake(
        ModelEvent $eventModel,
    ): SetMessageEntrySlugContract {
        $entry = $eventModel->sender;

        $isInstance = $entry instanceof Entry;

        if (! $isInstance) {
            return new DoNotSetSlug();
        }

        assert($entry instanceof Entry);

        if (
            ! in_array(
                $entry->getSection()->handle,
                [
                    'messages',
                    'internalMessages',
                ],
                true
            )
        ) {
            return new DoNotSetSlug();
        }

        $postDate = $entry->postDate;

        $postDateIsDateTime = $postDate instanceof DateTime;

        if (! $postDateIsDateTime) {
            return new DoNotSetSlug();
        }

        return new SetMessageEntrySlug(
            $entry,
        );
    }
}
