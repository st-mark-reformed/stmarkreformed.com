<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Craft\Behaviors\ProfileEntriesBehavior;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\fields\data\SingleOptionFieldData;

use function array_keys;
use function array_map;
use function assert;
use function in_array;

class RetrieveSpeakerOptions
{
    public function __construct(private EntryQueryFactory $queryFactory)
    {
    }

    /**
     * @param string[] $selectedSlugs
     *
     * @throws InvalidFieldException
     */
    public function retrieve(array $selectedSlugs = []): OptionGroupCollection
    {
        $query = $this->queryFactory->make();

        $query->section('profiles');

        $query->orderBy('lastName asc');

        /** @phpstan-ignore-next-line */
        $query->hasMessages(true);

        /** @var array<array-key, Entry&ProfileEntriesBehavior> $speakers */
        $speakers = $query->all();

        $groupsArrays = [
            'St. Mark Leadership' => [],
            'Other Speakers' => [],
        ];

        foreach ($speakers as $speaker) {
            $position = $speaker->getFieldValue(
                'leadershipPosition'
            );

            assert($position instanceof SingleOptionFieldData);

            $key = $position->value === null ?
                'Other Speakers' :
                'St. Mark Leadership';

            $slug = (string) $speaker->slug;

            $groupsArrays[$key][] = new SelectOption(
                name: $speaker->fullNameHonorificAppendedPosition(),
                slug: $slug,
                isActive: in_array(
                    $slug,
                    $selectedSlugs,
                    true,
                ),
            );
        }

        $speakerGroups = array_map(
            static fn (
                string $groupTitle,
                array $speakers,
            ) => new OptionGroup(
                groupTitle: $groupTitle,
                selectOptions: $speakers,
            ),
            array_keys($groupsArrays),
            $groupsArrays
        );

        return new OptionGroupCollection(
            optionGroups: $speakerGroups,
        );
    }
}
