<?php

declare(strict_types=1);

namespace App\Series\Admin\EditSeries\PostEditSeries;

use App\EmptyUuid;
use App\Series\Series;
use App\Series\SeriesSlug;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class SeriesFactory
{
    public function createFromRequest(ServerRequest $request): Series
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString('seriesId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new Series(
            id: $id,
            title: $request->parsedBody->getString(name: 'title'),
            slug: new SeriesSlug(
                slug: $request->parsedBody->getString(name: 'slug'),
            ),
        );
    }
}
