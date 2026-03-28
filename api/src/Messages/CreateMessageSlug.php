<?php

declare(strict_types=1);

namespace App\Messages;

use Cocur\Slugify\Slugify;

use function implode;

readonly class CreateMessageSlug
{
    public static function create(NewMessage|Message $message): string
    {
        return implode('-', [
            $message->date->format('Y-m-d'),
            new Slugify()->slugify($message->title),
        ]);
    }
}
