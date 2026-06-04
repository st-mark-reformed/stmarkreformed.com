<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin\EditHymnOfTheMonthItem\PostEditHymnOfTheMonthItem;

use App\EmptyUuid;
use App\HymnsOfTheMonth\Admin\HymnUploadResolver;
use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class HymnOfTheMonthItemFactory
{
    public function __construct(private HymnUploadResolver $uploadResolver)
    {
    }

    public function createFromRequest(ServerRequest $request): HymnOfTheMonthItem
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString(name: 'hymnOfTheMonthId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        $date = $this->getDate(
            month: $request->parsedBody->getString(name: 'month'),
        );

        // Files are stored under the derived slug, so resolve it up front.
        $slug = new HymnOfTheMonthItem(date: $date)->slug;

        return new HymnOfTheMonthItem(
            id: $id,
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
