<?php

declare(strict_types=1);

namespace Config;

readonly class ConfigPath
{
    public const string PATH = __DIR__;

    public const string DEPENDENCIES = self::PATH . '/Dependencies';

    public const string EVENTS = self::PATH . '/Events';
}
