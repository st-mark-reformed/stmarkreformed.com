<?php

declare(strict_types=1);

namespace App\Html;

readonly class HtmlPath
{
    public const string PATH = __DIR__;

    public const string HTML_LAYOUT = self::PATH . '/HtmlLayout.phtml';

    public const string HTML_FORM_LAYOUT = self::PATH . '/HtmlFormLayout.phtml';

    public const string CENTERED_WRAPPER_LAYOUT = self::PATH . '/CenteredWrapperLayout.phtml';
}
