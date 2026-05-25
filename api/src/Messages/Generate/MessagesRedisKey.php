<?php

declare(strict_types=1);

namespace App\Messages\Generate;

final class MessagesRedisKey
{
    private const string NAMESPACE = 'api-messages:';

    public static function page(int $pageNum): string
    {
        return self::NAMESPACE . 'page:' . $pageNum;
    }

    public static function pagePattern(): string
    {
        return self::NAMESPACE . 'page:*';
    }

    public static function slug(string $messageSlug): string
    {
        return self::NAMESPACE . 'slug:' . $messageSlug;
    }

    public static function slugPattern(): string
    {
        return self::NAMESPACE . 'slug:*';
    }

    public static function bySpeakerPage(string $speakerSlug, int $pageNum): string
    {
        return self::NAMESPACE . 'by:' . $speakerSlug . ':' . $pageNum;
    }

    public static function bySpeakerPattern(string $speakerSlug): string
    {
        return self::NAMESPACE . 'by:' . $speakerSlug . ':*';
    }

    public static function bySeriesPage(string $seriesSlug, int $pageNum): string
    {
        return self::NAMESPACE . 'series:' . $seriesSlug . ':' . $pageNum;
    }

    public static function bySeriesPattern(string $seriesSlug): string
    {
        return self::NAMESPACE . 'series:' . $seriesSlug . ':*';
    }

    public static function mostRecentSeries(): string
    {
        return self::NAMESPACE . 'most_recent_series';
    }

    public static function bySpeakerOptions(): string
    {
        return self::NAMESPACE . 'by_options';
    }

    public static function bySeriesOptions(): string
    {
        return self::NAMESPACE . 'series_options';
    }
}
