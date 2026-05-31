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
    case checkbox;

    public function templatePath(): string
    {
        return match ($this) {
            self::hidden => __DIR__ . '/HtmlFormInputHidden.phtml',
            self::checkbox => __DIR__ . '/HtmlFormInputCheckbox.phtml',
            default => __DIR__ . '/HtmlFormInputText.phtml',
        };
    }
}
