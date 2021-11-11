<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use craft\elements\Entry;

interface IndexMessageEntryContract
{
    public function index(Entry $message): void;
}
