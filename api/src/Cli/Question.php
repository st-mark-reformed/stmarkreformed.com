<?php

declare(strict_types=1);

namespace App\Cli;

interface Question
{
    public function ask(
        string $question,
        bool $required = false,
        bool $hidden = false,
    ): string;
}
