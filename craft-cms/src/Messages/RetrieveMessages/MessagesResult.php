<?php

declare(strict_types=1);

namespace App\Messages\RetrieveMessages;

use craft\elements\Entry;

use function array_map;
use function count;

class MessagesResult
{
    /** @var Entry[] */
    private array $messages = [];

    /**
     * @param Entry[] $messages
     */
    public function __construct(
        private int $absoluteTotal,
        array $messages,
    ) {
        array_map(
            function (Entry $message): void {
                $this->messages[] = $message;
            },
            $messages,
        );
    }

    public function absoluteTotal(): int
    {
        return $this->absoluteTotal;
    }

    /**
     * @return Entry[]
     */
    public function messages(): array
    {
        return $this->messages;
    }

    public function count(): int
    {
        return count($this->messages());
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callback): array
    {
        return array_map(
            $callback,
            $this->messages(),
        );
    }

    public function first(): Entry
    {
        return $this->messages()[0];
    }
}
