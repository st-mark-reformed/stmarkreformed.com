<?php

declare(strict_types=1);

namespace App\Html;

use function array_map;
use function array_values;

readonly class ButtonRow
{
    /** @var ButtonConfig[] */
    public array $buttons;

    /** @param ButtonConfig[] $buttons */
    public function __construct(array $buttons)
    {
        $this->buttons = array_values(array_map(
            static fn (ButtonConfig $b) => $b,
            $buttons,
        ));
    }
}
