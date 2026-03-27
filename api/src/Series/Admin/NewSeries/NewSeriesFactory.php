<?php

declare(strict_types=1);

namespace App\Series\Admin\NewSeries;

use App\Series\NewSeries;
use App\Series\SeriesSlug;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewSeriesFactory
{
    public function createFromRequest(ServerRequest $request): NewSeries
    {
        return new NewSeries(
            title: $request->parsedBody->getString(name: 'title'),
            slug: new SeriesSlug(
                slug: $request->parsedBody->getString(name: 'slug'),
            ),
        );
    }
}
