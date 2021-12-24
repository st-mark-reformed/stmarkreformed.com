<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;

class IndexMessage
{
    public function __construct(private IndexMessageEntryFactory $factory)
    {
    }

    public function index(Entry $message): void
    {
        $this->factory->make(message: $message)->index(message: $message);
    }
}
