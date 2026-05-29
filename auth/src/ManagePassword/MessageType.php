<?php

declare(strict_types=1);

namespace App\ManagePassword;

enum MessageType: string
{
    case success = 'success';
    case error   = 'error';
}
