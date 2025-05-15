<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

readonly class PostContactFormResponder
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function respond(Result $result): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(
            $result->success ? 200 : 400,
        );

        $response->getBody()->write(
            (string) json_encode($result->asScalarArray()),
        );

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}
