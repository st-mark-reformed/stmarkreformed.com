<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Generate;

use function str_starts_with;

final class MenOfTheMarkRedisKey
{
    private const string NAMESPACE = 'api-publications:men_of_the_mark:';

    public static function index(): string
    {
        return self::NAMESPACE . 'index';
    }

    public static function slug(string $slug): string
    {
        return self::NAMESPACE . 'slug:' . $slug;
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
