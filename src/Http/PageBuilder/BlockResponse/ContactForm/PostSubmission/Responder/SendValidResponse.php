<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class SendValidResponse implements ResponderContract
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function respond(SendEmailResult $result): ResponseInterface
    {
        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                $result->formValues()->redirectUrl()->toString(),
            );
    }
}
