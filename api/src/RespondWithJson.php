<?php

declare(strict_types=1);

namespace App;

use JsonSerializable;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

readonly class RespondWithJson implements Responder
{
    public function __construct(
        private JsonSerializable $entity,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function respond(): ResponseInterface
    {
        $response = $this->factory->createResponse();

        $response->getBody()->write((string) json_encode(
            $this->entity->jsonSerialize(),
        ));

        return $response->withHeader('Content-type', 'application/json');
    }
}
