<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn\Response;

use App\Http\Response\LogIn\LogInPayload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages as FlashMessages;

class RespondWithError implements LogInResponderContract
{
    public function __construct(
        private FlashMessages $flashMessages,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function respond(
        string $redirectTo,
        LogInPayload $payload,
    ): ResponseInterface {
        $this->flashMessages->addMessage(
            'FormMessage',
            [
                'status' => 'error',
                'message' => $payload->message(),
            ],
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', $redirectTo);
    }
}
