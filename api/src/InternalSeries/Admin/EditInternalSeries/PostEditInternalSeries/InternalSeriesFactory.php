<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\EditInternalSeries\PostEditInternalSeries;

use App\EmptyUuid;
use App\InternalSeries\InternalSeriesSlug;
use App\InternalSeries\PopulatedInternalSeries;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class InternalSeriesFactory
{
    public function createFromRequest(
        ServerRequest $request,
    ): PopulatedInternalSeries {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString('seriesId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new PopulatedInternalSeries(
            id: $id,
            title: $request->parsedBody->getString(name: 'title'),
            slug: new InternalSeriesSlug(
                slug: $request->parsedBody->getString(name: 'slug'),
            ),
        );
    }
}
