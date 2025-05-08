<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use App\Shared\FieldHandlers\Categories\CategoriesFieldHandler;
use App\Shared\FieldHandlers\Entry\EntryFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Tags\TagsFieldHandler;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\Tag;
use craft\errors\InvalidFieldException;

use function array_map;
use function implode;

class CreateMessageIndexBody
{
    public function __construct(
        private GenericHandler $genericHandler,
        private TagsFieldHandler $tagsFieldHandler,
        private EntryFieldHandler $entryFieldHandler,
        private CategoriesFieldHandler $categoriesFieldHandler,
    ) {
    }

    /**
     * @return mixed[]
     *
     * @throws InvalidFieldException
     */
    public function fromMessage(Entry $message): array
    {
        $speakers = $this->entryFieldHandler->getAll(
            element: $message,
            field: 'profile',
        );

        $speakerNames = implode(', ', array_map(
            static fn (Entry $s) => (string) $s->title,
            $speakers,
        ));

        $speakerSlugs = implode(', ', array_map(
            static fn (Entry $s) => (string) $s->slug,
            $speakers,
        ));

        $speakerIds = implode(', ', array_map(
            static fn (Entry $s) => (string) $s->uid,
            $speakers,
        ));

        $messageText = $this->genericHandler->getString(
            element: $message,
            field: 'messageText',
        );

        $series = $this->categoriesFieldHandler->getAll(
            element: $message,
            field: 'messageSeries',
        );

        $seriesNames = implode(', ', array_map(
            static fn (Category $c) => (string) $c->title,
            $series,
        ));

        $seriesSlugs = implode(', ', array_map(
            static fn (Category $c) => (string) $c->slug,
            $series,
        ));

        $seriesIds = implode(', ', array_map(
            static fn (Category $c) => (string) $c->uid,
            $series,
        ));

        $shortDescription = $this->genericHandler->getString(
            element: $message,
            field: 'shortDescription',
        );

        $tags = $this->tagsFieldHandler->getAll(
            element: $message,
            field: 'keywords',
        );

        $tagNames = implode(', ', array_map(
            static fn (Tag $t) => (string) $t->title,
            $tags,
        ));

        $tagIds = implode(', ', array_map(
            static fn (Tag $t) => (string) $t->uid,
            $tags,
        ));

        return [
            'title' => (string) $message->title,
            'speakerName' => $speakerNames,
            'speakerSlug' => $speakerSlugs,
            'speakerId' => $speakerIds,
            'messageText' => $messageText,
            'messageSeries' => $seriesNames,
            'messageSeriesSlug' => $seriesSlugs,
            'messageSeriesId' => $seriesIds,
            'shortDescription' => $shortDescription,
            'tags' => $tagNames,
            'tagIds' => $tagIds,
        ];
    }
}
