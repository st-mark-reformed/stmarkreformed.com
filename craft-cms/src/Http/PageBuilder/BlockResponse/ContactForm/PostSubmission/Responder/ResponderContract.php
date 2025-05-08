<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use Psr\Http\Message\ResponseInterface;

interface ResponderContract
{
    public function respond(SendEmailResult $result): ResponseInterface;
}
