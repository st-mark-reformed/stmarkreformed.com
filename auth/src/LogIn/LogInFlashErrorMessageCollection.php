<?php

declare(strict_types=1);

namespace App\LogIn;

use function array_map;
use function array_values;
use function count;

readonly class LogInFlashErrorMessageCollection
{
    /** @var LogInFlashErrorMessage[] */
    public array $messages;

    /** @param LogInFlashErrorMessage[] $messages */
    public function __construct(array $messages = [])
    {
        $this->messages = array_values(array_map(
            static fn (LogInFlashErrorMessage $m) => $m,
            $messages,
        ));
    }

    public function count(): int
    {
        return count($this->messages);
    }

    public function hasMessages(): bool
    {
        return $this->count() > 0;
    }

    public function walk(callable $callback): void
    {
        array_map($callback, $this->messages);
    }
}
