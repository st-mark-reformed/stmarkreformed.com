<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Craft\Behaviors\ProfileEntriesBehavior;
use App\Http\Components\Link\Link;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;

use function array_map;
use function http_build_query;

class RetrieveLeadersWithMessages
{
    public function __construct(private EntryQueryFactory $queryFactory)
    {
    }

    /**
     * @return Link[]
     */
    public function retrieve(): array
    {
        $query = $this->queryFactory->make();

        $query->section('profiles');

        $query->orderBy('lastName asc');

        /** @phpstan-ignore-next-line */
        $query->leadershipPosition([
            'pastor',
            'assistantPastor',
            'associatePastor',
            'elder',
            'rulingElder',
            'deacon',
        ]);

        /** @phpstan-ignore-next-line */
        $query->hasMessages(true);

        /** @var array<array-key, Entry&ProfileEntriesBehavior> $leaders */
        $leaders = $query->all();

        return array_map(
            /**
             * @param Entry&ProfileEntriesBehavior $leader
             */
            static function (mixed $leader): Link {
                return new Link(
                    isEmpty: false,
                    content: $leader->fullNameHonorificAppendedPosition(),
                    href: '/media/messages?' . http_build_query([
                        'by' => [(string) $leader->slug],
                    ]),
                );
            },
            $leaders,
        );
    }
}
