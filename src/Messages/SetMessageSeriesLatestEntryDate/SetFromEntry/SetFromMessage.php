<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use craft\elements\Category;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\services\Elements as ElementsService;
use DateTimeInterface;
use Throwable;
use yii\base\Exception;

use function assert;

class SetFromMessage implements SetFromMessageContract
{
    public function __construct(private ElementsService $elementsService)
    {
    }

    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     *
     * @phpstan-ignore-next-line
     */
    public function set(Category $series, ?Entry $message): void
    {
        assert($message instanceof Entry);

        $messageDate = $message->postDate;

        assert($messageDate instanceof DateTimeInterface);

        $series->setFieldValue(
            'latestEntryAt',
            $messageDate,
        );

        $this->elementsService->saveElement($series);
    }
}
