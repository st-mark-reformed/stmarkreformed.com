<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Message;

readonly class IndexMessage
{
    public function __construct(private IndexMessageFromRecordFactory $factory)
    {
    }

    public function index(Message $message): void
    {
        $this->factory->make(message: $message)->index(message: $message);
    }
}
