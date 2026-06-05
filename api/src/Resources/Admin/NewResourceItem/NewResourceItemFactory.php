<?php

declare(strict_types=1);

namespace App\Resources\Admin\NewResourceItem;

use App\Resources\Admin\ResourceUploadResolver;
use App\Resources\NewResourceItem;
use DateTimeImmutable;
use DateTimeZone;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewResourceItemFactory
{
    public function __construct(private ResourceUploadResolver $uploadResolver)
    {
    }

    public function createFromRequest(ServerRequest $request): NewResourceItem
    {
        $title        = $request->parsedBody->getString(name: 'title');
        $providedSlug = $request->parsedBody->getString(name: 'slug');

        // Files are stored under the slug, so resolve it up front.
        $slug = new NewResourceItem(
            title: $title,
            slug: $providedSlug === '' ? null : $providedSlug,
        )->slug;

        return new NewResourceItem(
            isEnabled: $request->parsedBody->getBoolean(name: 'isEnabled'),
            date: $this->getDate(
                date: $request->parsedBody->getString(name: 'date'),
            ),
            title: $title,
            slug: $slug,
            body: $request->parsedBody->getString(name: 'body'),
            downloads: $this->uploadResolver->resolveDownloads(
                slug: $slug,
                request: $request,
            ),
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
