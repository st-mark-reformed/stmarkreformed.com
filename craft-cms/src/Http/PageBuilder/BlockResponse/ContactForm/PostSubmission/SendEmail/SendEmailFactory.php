<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;

class SendEmailFactory
{
    public function __construct(
        private SendEmail $sendEmail,
        private InsufficientInputToSendEmail $insufficientInputToSendEmail,
    ) {
    }

    public function make(FormValues $formValues): SendEmailContract
    {
        if ($formValues->isNotValid()) {
            return $this->insufficientInputToSendEmail;
        }

        return $this->sendEmail;
    }
}
