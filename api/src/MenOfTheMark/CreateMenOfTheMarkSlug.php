<?php

declare(strict_types=1);

namespace App\MenOfTheMark;

use Cocur\Slugify\Slugify;

readonly class CreateMenOfTheMarkSlug
{
    public static function create(
        NewMenOfTheMarkItem|MenOfTheMarkItem $menOfTheMarkItem,
    ): string {
        return new Slugify()->slugify($menOfTheMarkItem->title);
    }
}
