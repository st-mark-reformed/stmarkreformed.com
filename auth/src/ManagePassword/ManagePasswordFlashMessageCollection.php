<?php

declare(strict_types=1);

namespace App\ManagePassword;

use function array_filter;
use function array_map;
use function array_values;
use function count;

readonly class ManagePasswordFlashMessageCollection
{
    /** @var ManagePasswordFlashMessage[] */
    public array $messages;

    /** @param ManagePasswordFlashMessage[] $messages */
    public function __construct(array $messages = [])
    {
        $this->messages = array_values(array_map(
            static fn (ManagePasswordFlashMessage $m) => $m,
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

    public function ofType(MessageType $type): self
    {
        return new self(array_filter(
            $this->messages,
            static fn (ManagePasswordFlashMessage $m) => $m->type === $type,
        ));
    }
}
