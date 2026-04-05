<?php

declare(strict_types=1);

namespace App\Messages\Search;

enum MessagesSearchIndex: string
{
    case MESSAGES = 'api-messages';
}
