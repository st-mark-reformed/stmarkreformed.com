<?php

declare(strict_types=1);

namespace App\News\Admin\NewNewsItem;

use App\News\NewNewsItem;
use DateTimeImmutable;
use DateTimeZone;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewNewsItemFactory
{
    public function createFromRequest(ServerRequest $request): NewNewsItem
    {
        return new NewNewsItem(
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
