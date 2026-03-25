<?php

declare(strict_types=1);

namespace App\Result;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

readonly class ResultResponder
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function respond(Result $result): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write(
            (string) json_encode($result->asArray()),
        );

        return $response->withHeader('Content-type', 'application/json');
    }
}
