<?php

declare(strict_types=1);

namespace App\News\Admin\EditNewsItem\PostEditNewsItem;

use App\EmptyUuid;
use App\News\NewsItem;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class NewsItemFactory
{
    public function createFromRequest(ServerRequest $request): NewsItem
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString(name: 'newsId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new NewsItem(
            id: $id,
            isEnabled: $request->parsedBody->getBoolean(name: 'isEnabled'),
            date: $this->getDate(
                date: $request->parsedBody->getString(name: 'date'),
            ),
            title: $request->parsedBody->getString(name: 'title'),
            slug: $request->parsedBody->getString(name: 'slug'),
            heading: $request->parsedBody->getString(name: 'heading'),
            subheading: $request->parsedBody->getString(name: 'subheading'),
            body: $request->parsedBody->getString(name: 'body'),
        );
    }

    private function getDate(string $date): DateTimeImmutable
    {
        $dateObj = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            $date,
            new DateTimeZone('US/Central'),
        );

        if ($dateObj === false) {
            $dateObj = DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i',
                $date,
                new DateTimeZone('US/Central'),
            );
        }

        if ($dateObj === false) {
            $dateObj = new DateTimeImmutable()->setTimezone(
                new DateTimeZone('US/Central'),
            );
        }

        return $dateObj;
    }
}
