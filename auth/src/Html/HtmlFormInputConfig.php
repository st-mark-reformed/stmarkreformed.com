<?php

declare(strict_types=1);

namespace App\Html;

readonly class HtmlFormInputConfig
{
    public function __construct(
        public string $label,
        public string $name,
        public string $value = '',
        public HtmlFormInputType $type = HtmlFormInputType::text,
        public ButtonConfig|null $rightSideButton = null,
        public bool $required = false,
    ) {
    }
}
