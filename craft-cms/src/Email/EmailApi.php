<?php

declare(strict_types=1);

namespace App\Email;

use App\Email\Entities\Email;
use App\Email\Entities\EmailResult;
use App\Email\Queue\SendEmailQueueJob;
use craft\queue\Queue;

class EmailApi
{
    public function __construct(
        private Queue $queue,
        private SendMailContract $sendMail,
    ) {
    }

    public function send(Email $email): EmailResult
    {
        return $this->sendMail->send(email: $email);
    }

    public function enqueue(Email $email): void
    {
        $this->queue->push(new SendEmailQueueJob(
            email: $email,
        ));
    }
}
