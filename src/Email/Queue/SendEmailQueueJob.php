<?php

declare(strict_types=1);

namespace App\Email\Queue;

use App\Email\Entities\Email;
use App\Email\SendMailContract;
use Config\di\Container;
use craft\queue\BaseJob;

use function assert;

class SendEmailQueueJob extends BaseJob
{
    public function __construct(private Email $email)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $sendMail = Container::get()->get(SendMailContract::class);

        assert($sendMail instanceof SendMailContract);

        $sendMail->send(email: $this->email);
    }
}
