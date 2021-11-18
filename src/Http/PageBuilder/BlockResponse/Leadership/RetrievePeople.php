<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use App\Craft\Behaviors\ProfileEntriesBehavior;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;

// phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType

class RetrievePeople
{
    public function __construct(
        private GenericHandler $genericHandler,
        private EntryQueryFactory $entryQueryFactory,
        private AssetsFieldHandler $assetsFieldHandler,
    ) {
    }

    /**
     * @return LeadershipPersonContentModel[]
     */
    public function retrieve(string $position): array
    {
        $query = $this->entryQueryFactory->make();

        $query->section('profiles');

        /** @phpstan-ignore-next-line */
        $query->leadershipPosition($position);

        /**
         * @var array<array-key, Entry&ProfileEntriesBehavior> $entries
         */
        $entries = $query->all();

        return array_map(
            [$this, 'createPersonModel'],
            $entries,
        );
    }

    /**
     * @param Entry&ProfileEntriesBehavior $entry
     *
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    private function createPersonModel(
        mixed $entry
    ): LeadershipPersonContentModel {
        $imageAsset = $this->assetsFieldHandler->getOneOrNull(
            element: $entry,
            field: 'profilePhoto',
        );

        $imageUrl = '';

        if ($imageAsset !== null) {
            $imageUrl = (string) $imageAsset->getUrl();
        }

        $bio = $this->genericHandler->getTwigMarkup(
            element: $entry,
            field: 'bio',
        );

        return new LeadershipPersonContentModel(
            imageUrl: $imageUrl,
            title: $entry->fullNameHonorific(),
            content: $bio,
        );
    }
}
