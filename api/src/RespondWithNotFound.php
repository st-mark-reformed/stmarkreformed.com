<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

readonly class RespondWithNotFound implements Responder
{
    public function __construct(
        private ResponseFactoryInterface $factory,
        private string $message = 'Not found',
    ) {
    }

    public function respond(): ResponseInterface
    {
        $response = $this->factory->createResponse(404);

        $response->getBody()->write((string) json_encode([
            'success' => false,
            'status' => 'error',
            'message' => $this->message,
        ]));

        return $response->withHeader('Content-type', 'application/json');
    }
}
