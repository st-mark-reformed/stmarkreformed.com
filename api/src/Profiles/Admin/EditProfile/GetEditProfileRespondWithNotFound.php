<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile;

use App\Responder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

readonly class GetEditProfileRespondWithNotFound implements Responder
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function respond(): ResponseInterface
    {
        $response = $this->factory->createResponse(404);

        $response->getBody()->write((string) json_encode([
            'success' => false,
            'status' => 'error',
            'message' => 'Profile not found',
        ]));

        return $response->withHeader('Content-type', 'application/json');
    }
}
