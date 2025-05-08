<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;

class ResponderFactory
{
    public function __construct(
        private SendValidResponse $sendValidResponse,
        private SendInvalidResponse $sendInvalidResponse,
    ) {
    }

    public function make(SendEmailResult $result): ResponderContract
    {
        if ($result->sentSuccessfully()) {
            return $this->sendValidResponse;
        }

        return $this->sendInvalidResponse;
    }
}
