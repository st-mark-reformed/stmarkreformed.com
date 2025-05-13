<?php

declare(strict_types=1);

namespace Cli;

use function count;
use function explode;

readonly class Alias
{
    /** @var string[] */
    public array $to;

    /** @param string[] $to */
    public function __construct(public string $from, array|null $to = null)
    {
        if ($to === null) {
            $to = explode(':', $from);
        }

        $this->to = $to;
    }

    public function toCount(): int
    {
        return count($this->to);
    }
}
