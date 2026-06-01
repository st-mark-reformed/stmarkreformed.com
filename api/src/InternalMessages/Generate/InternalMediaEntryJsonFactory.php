<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use App\InternalMessages\InternalMessage;
use App\InternalSeries\InternalSeries;
use App\Profiles\Profile;

readonly class InternalMediaEntryJsonFactory
{
    /**
     * @return array{
     *     uid: string,
     *     title: string,
     *     slug: string,
     *     postDate: string,
     *     postDateDisplay: string,
     *     by: array{title: string, slug: string}|null,
     *     text: string,
     *     series: array{title: string, slug: string}|null,
     *     audioFileName: string,
     *     audioFileSize: int,
     * }
     */
    public function create(InternalMessage $message): array
    {
        return [
            'uid' => $message->id->toString(),
            'title' => $message->title,
            'slug' => $message->slug,
            'postDate' => $message->date->format('Y-m-d H:i:s'),
            'postDateDisplay' => $message->date->format('F j, Y'),
            'by' => $this->by(profile: $message->speaker),
            'text' => $message->passage,
            'series' => $this->series(series: $message->series),
            'audioFileName' => $message->audioPath,
            'audioFileSize' => $message->audioFileSize,
        ];
    }

    /** @return array{title: string, slug: string}|null */
    private function by(Profile $profile): array|null
    {
        if ($profile->isEmpty()) {
            return null;
        }

        return [
            'title' => $profile->fullNameWithHonorific,
            'slug' => $profile->slug,
        ];
    }

    /** @return array{title: string, slug: string}|null */
    private function series(InternalSeries $series): array|null
    {
        if ($series->isEmpty()) {
            return null;
        }

        return [
            'title' => $series->title,
            'slug' => $series->slug->toString(),
        ];
    }
}
