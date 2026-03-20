<?php

declare(strict_types=1);

namespace App\Html;

use function array_map;
use function array_values;

readonly class ButtonRows
{
    /** @var ButtonRow[] */
    public array $rows;

    /** @param ButtonRow[] $rows */
    public function __construct(array $rows = [])
    {
        $this->rows = array_values(array_map(
            static fn (ButtonRow $r) => $r,
            $rows,
        ));
    }
}
