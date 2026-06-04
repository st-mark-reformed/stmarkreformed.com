<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Generate;

use function str_starts_with;

final class HymnsOfTheMonthRedisKey
{
    private const string NAMESPACE = 'api-members:hymns_of_the_month:';

    public static function index(): string
    {
        return self::NAMESPACE . 'index';
    }

    public static function slug(string $hymnSlug): string
    {
        return self::NAMESPACE . 'slug:' . $hymnSlug;
    }

    public static function isSlugKey(string $key): bool
    {
        return str_starts_with($key, self::NAMESPACE . 'slug:');
    }

    public static function slugPattern(): string
    {
        return self::NAMESPACE . 'slug:*';
    }
}
