<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin\NewInternalSeries;

use App\InternalSeries\InternalSeriesSlug;
use App\InternalSeries\NewInternalSeries;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewInternalSeriesFactory
{
    public function createFromRequest(ServerRequest $request): NewInternalSeries
    {
        return new NewInternalSeries(
            title: $request->parsedBody->getString(name: 'title'),
            slug: new InternalSeriesSlug(
                slug: $request->parsedBody->getString(name: 'slug'),
            ),
        );
    }
}
