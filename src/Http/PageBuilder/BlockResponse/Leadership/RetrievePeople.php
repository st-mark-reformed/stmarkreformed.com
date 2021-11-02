<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;

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

        /**
         * @psalm-suppress UndefinedMagicMethod
         * @phpstan-ignore-next-line
         */
        $query->leadershipPosition($position);

        /**
         * @var Entry[] $entries
         */
        $entries = $query->all();

        return array_map(
            [$this, 'createPersonModel'],
            $entries,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    private function createPersonModel(Entry $entry): LeadershipPersonContentModel
    {
        $imageAsset = $this->assetsFieldHandler->getOneOrNull(
            element: $entry,
            field: 'profilePhoto',
        );

        $imageUrl = '';

        if ($imageAsset !== null) {
            $imageUrl = (string) $imageAsset->getUrl();
        }

        $honorific = $this->genericHandler->getString(
            element: $entry,
            field: 'titleOrHonorific',
        );

        $name = (string) $entry->title;

        if ($honorific !== '') {
            $name = $honorific . ' ' . $name;
        }

        $bio = $this->genericHandler->getTwigMarkup(
            element: $entry,
            field: 'bio',
        );

        return new LeadershipPersonContentModel(
            imageUrl: $imageUrl,
            title: $name,
            content: $bio,
        );
    }
}
