<?php

declare(strict_types=1);

namespace App\Series\Admin\EditSeries\PostEditSeries;

use App\EmptyUuid;
use App\Series\PopulatedSeries;
use App\Series\SeriesSlug;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class SeriesFactory
{
    public function createFromRequest(ServerRequest $request): PopulatedSeries
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString('seriesId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new PopulatedSeries(
            id: $id,
            title: $request->parsedBody->getString(name: 'title'),
            slug: new SeriesSlug(
                slug: $request->parsedBody->getString(name: 'slug'),
            ),
        );
    }
}
