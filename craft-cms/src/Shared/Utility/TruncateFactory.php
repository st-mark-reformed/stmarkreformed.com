<?php

declare(strict_types=1);

namespace App\Shared\Utility;

use TS\Text\Truncation;

class TruncateFactory
{
    public function make(
        int $limit,
        string $strategy = Truncation::STRATEGY_WORD,
        string $truncationString = '…',
        string $encoding = 'UTF-8',
        int $minLength = 0,
    ): Truncation {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Truncation(
            $limit,
            $strategy,
            $truncationString,
            $encoding,
            $minLength,
        );
    }
}
