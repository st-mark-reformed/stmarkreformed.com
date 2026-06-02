<?php

declare(strict_types=1);

namespace App\News;

readonly class NewsItemValidation
{
    /** @return string[] */
    public static function validate(NewNewsItem|NewsItem $newsItem): array
    {
        $messages = [];

        if ($newsItem->title === '') {
            $messages[] = 'Title is required';
        }

        if ($newsItem->body === '') {
            $messages[] = 'Body is required';
        }

        return $messages;
    }
}
