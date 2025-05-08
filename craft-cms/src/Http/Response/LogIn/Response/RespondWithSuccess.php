<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn\Response;

use App\Http\Response\LogIn\LogInPayload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class RespondWithSuccess implements LogInResponderContract
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function respond(
        string $redirectTo,
        LogInPayload $payload,
    ): ResponseInterface {
        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', $redirectTo);
    }
}
