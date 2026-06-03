<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

use App\MenOfTheMark\MenOfTheMarkItem;

readonly class MenOfTheMarkEntryJsonFactory
{
    /**
     * Shapes the public entry exactly as the front-end `PublicationEntry` type
     * expects (web/app/publications/PublicationEntry.ts): the body is the raw
     * HTML and `publicationDate` is a plain `Y-m-d H:i:s` string.
     *
     * @return array{
     *     uid: string,
     *     title: string,
     *     slug: string,
     *     publicationDate: string,
     *     bodyHtml: string,
     * }
     */
    public function create(MenOfTheMarkItem $menOfTheMarkItem): array
    {
        return [
            'uid' => $menOfTheMarkItem->id->toString(),
            'title' => $menOfTheMarkItem->title,
            'slug' => $menOfTheMarkItem->slug,
            'publicationDate' => $menOfTheMarkItem->date->format('Y-m-d H:i:s'),
            'bodyHtml' => $menOfTheMarkItem->body,
        ];
    }
}
