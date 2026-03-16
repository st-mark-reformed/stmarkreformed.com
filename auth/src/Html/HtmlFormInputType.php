<?php

declare(strict_types=1);

namespace App\Html;

enum HtmlFormInputType
{
    case text;
    case email;
    case password;
    case readonly;
    case hidden;

    public function templatePath(): string
    {
        return match ($this) {
            self::hidden => __DIR__ . '/HtmlFormInputHidden.phtml',
            default => __DIR__ . '/HtmlFormInputText.phtml',
        };
    }
}
