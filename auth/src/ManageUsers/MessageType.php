<?php

declare(strict_types=1);

namespace App\ManageUsers;

enum MessageType: string
{
    case success = 'success';
    case error   = 'error';
}
