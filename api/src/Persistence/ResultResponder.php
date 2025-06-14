<?php

declare(strict_types=1);

namespace App\Persistence;

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
        $httpResponseCode = $result->httpResponseCode ??
            ($result->success ? 200 : 400);

        $response = $this->responseFactory->createResponse(
            $httpResponseCode,
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
