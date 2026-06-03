<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use function str_starts_with;

final class PastorsPageRedisKey
{
    private const string NAMESPACE = 'api-pastorsPage:';

    public static function page(int $pageNum): string
    {
        return self::NAMESPACE . 'page:' . $pageNum;
    }

    public static function isPageKey(string $key): bool
    {
        return str_starts_with($key, self::NAMESPACE . 'page:');
    }

    public static function slug(string $pastorsPageSlug): string
    {
        return self::NAMESPACE . 'slug:' . $pastorsPageSlug;
    }

    public static function isSlugKey(string $key): bool
    {
        return str_starts_with($key, self::NAMESPACE . 'slug:');
    }

    public static function allPattern(): string
    {
        return self::NAMESPACE . '*';
    }
}
