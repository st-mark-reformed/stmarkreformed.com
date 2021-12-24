<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;

interface SendEmailContract
{
    public function send(FormValues $formValues): SendEmailResult;
}
