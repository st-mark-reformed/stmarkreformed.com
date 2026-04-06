<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Message;

interface IndexMessageFromRecord
{
    public function index(Message $message): void;
}
