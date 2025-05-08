<?php

declare(strict_types=1);

namespace App\Email;

use App\Email\Entities\Email;
use App\Email\Entities\EmailResult;

interface SendMailContract
{
    public function send(Email $email): EmailResult;
}
