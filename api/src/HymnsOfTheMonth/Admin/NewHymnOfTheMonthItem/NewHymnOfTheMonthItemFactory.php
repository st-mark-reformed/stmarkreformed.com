<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin\NewHymnOfTheMonthItem;

use App\HymnsOfTheMonth\Admin\HymnUploadResolver;
use App\HymnsOfTheMonth\NewHymnOfTheMonthItem;
use DateTimeImmutable;
use DateTimeZone;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewHymnOfTheMonthItemFactory
{
    public function __construct(private HymnUploadResolver $uploadResolver)
    {
    }

    public function createFromRequest(ServerRequest $request): NewHymnOfTheMonthItem
    {
        $date = $this->getDate(
            month: $request->parsedBody->getString(name: 'month'),
        );

        // Files are stored under the derived slug, so resolve it up front.
        $slug = new NewHymnOfTheMonthItem(date: $date)->slug;

        return new NewHymnOfTheMonthItem(
            isEnabled: $request->parsedBody->getBoolean(name: 'isEnabled'),
            date: $date,
            hymnPsalmName: $request->parsedBody->getString(name: 'hymnPsalmName'),
            musicSheetPath: $this->uploadResolver->resolveMusicSheetPath(
                slug: $slug,
                request: $request,
            ),
            practiceTracks: $this->uploadResolver->resolvePracticeTracks(
                slug: $slug,
                request: $request,
            ),
        );
    }

    private function getDate(string $month): DateTimeImmutable
    {
        $dateObj = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $month . '-01',
            new DateTimeZone('US/Central'),
        );

        if ($dateObj === false) {
            return new DateTimeImmutable()->setTimezone(
                new DateTimeZone('US/Central'),
            );
        }

        return $dateObj;
    }
}
