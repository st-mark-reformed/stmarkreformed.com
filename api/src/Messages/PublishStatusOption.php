<?php

declare(strict_types=1);

namespace App\Messages;

enum PublishStatusOption
{
    case PUBLISHED;
    case NOT_PUBLISHED;
    case ALL;
}
