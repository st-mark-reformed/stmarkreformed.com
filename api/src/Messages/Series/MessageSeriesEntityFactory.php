<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Messages\Series\MessageSeries\Slug;
use App\Messages\Series\MessageSeries\Title;
use Cocur\Slugify\Slugify;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;

readonly class MessageSeriesEntityFactory
{
    public function fromServerRequest(
        ServerRequestInterface $request,
    ): MessageSeries {
        $submittedData = $request->getParsedBody();
        $submittedData = is_array($submittedData) ? $submittedData : [];

        $title = $submittedData['title'] ?? '';

        $slug = $submittedData['slug'] ?? '';

        if ($slug === '') {
            $slug = Slugify::create()->slugify($title);
        }

        return new MessageSeries(
            title: new Title($title),
            slug: new Slug($slug),
        );
    }
}
