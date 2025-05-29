<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Entry;

use function array_map;

class FetchMenOfTheMark
{
    public function __construct(
        private GenericHandler $fieldHandler,
        private EntryQueryFactory $queryFactory,
    ) {
    }

    public function fetch(): Publications
    {
        $results = $this->queryFactory->make()
            ->section('menOfTheMark')
            ->limit(999999)
            ->all();

        return new Publications(
            array_map(
                fn (Entry $entry) => new Publication(
                    (string) $entry->title,
                    (string) $entry->slug,
                    (string) $entry->getUrl(),
                    $this->fieldHandler->getString(
                        $entry,
                        'body',
                    ),
                    $this->fieldHandler->entryPostDate(
                        $entry,
                    ),
                    (string) $entry->uid,
                ),
                $results,
            )
        );
    }
}
