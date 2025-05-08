<?php

declare(strict_types=1);

namespace App\Profiles\SetHasMessages;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;

use function array_map;

class SetHasMessagesOnAllProfiles
{
    public function __construct(
        private EntryQueryFactory $queryFactory,
        private SetHasMessagesOnAProfile $setHasMessagesOnAProfile,
    ) {
    }

    public function set(): void
    {
        $query = $this->queryFactory->make();

        $query->section('profiles');

        /** @var Entry[] $entries */
        $entries = $query->all();

        array_map(
            [$this->setHasMessagesOnAProfile, 'set'],
            $entries,
        );
    }
}
