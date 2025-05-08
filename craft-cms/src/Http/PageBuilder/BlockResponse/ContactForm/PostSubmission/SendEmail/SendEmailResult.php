<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;

class SendEmailResult
{
    public function __construct(
        private bool $sentSuccessfully,
        private FormValues $formValues,
    ) {
    }

    public function sentSuccessfully(): bool
    {
        return $this->sentSuccessfully;
    }

    public function formValues(): FormValues
    {
        return $this->formValues;
    }
}
