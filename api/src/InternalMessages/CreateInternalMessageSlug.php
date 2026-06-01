<?php

declare(strict_types=1);

namespace App\InternalMessages;

use Cocur\Slugify\Slugify;

use function implode;

readonly class CreateInternalMessageSlug
{
    public static function create(
        NewInternalMessage|InternalMessage $message,
    ): string {
        return implode('-', [
            $message->date->format('Y-m-d'),
            new Slugify()->slugify($message->title),
        ]);
    }
}
