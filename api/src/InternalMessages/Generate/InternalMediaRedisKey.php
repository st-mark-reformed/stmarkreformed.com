<?php

declare(strict_types=1);

namespace App\InternalMessages\Generate;

use function str_starts_with;

final class InternalMediaRedisKey
{
    private const string NAMESPACE = 'api-members:internal_media:';

    public static function page(int $pageNum): string
    {
        return self::NAMESPACE . 'page:' . $pageNum;
    }

    public static function isPageKey(string $key): bool
    {
        return str_starts_with($key, self::NAMESPACE . 'page:');
    }

    public static function slug(string $messageSlug): string
    {
        return self::NAMESPACE . 'slug:' . $messageSlug;
    }

    public static function isSlugKey(string $key): bool
    {
        return str_starts_with($key, self::NAMESPACE . 'slug:');
    }

    public static function bySpeakerPage(string $speakerSlug, int $pageNum): string
    {
        return self::NAMESPACE . 'by:' . $speakerSlug . ':' . $pageNum;
    }

    public static function bySeriesPage(string $seriesSlug, int $pageNum): string
    {
        return self::NAMESPACE . 'series:' . $seriesSlug . ':' . $pageNum;
    }

    public static function allPattern(): string
    {
        return self::NAMESPACE . '*';
    }
}
