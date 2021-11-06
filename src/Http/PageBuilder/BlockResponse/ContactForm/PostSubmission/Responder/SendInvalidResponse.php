<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;

class SendInvalidResponse implements ResponderContract
{
    public function __construct(
        private FlashMessages $flashMessages,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function respond(SendEmailResult $result): ResponseInterface
    {
        $this->flashMessages->addMessage(
            'ContactFormMessage',
            $result,
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader(
                'Location',
                $result->formValues()->fromUrl()->toString(),
            );
    }
}
