<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile;

use App\Profiles\Profile;
use App\Responder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

readonly class GetEditProfileRespondWithProfile implements Responder
{
    public function __construct(
        private Profile $profile,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function respond(): ResponseInterface
    {
        $response = $this->factory->createResponse();

        $response->getBody()->write((string) json_encode(
            $this->profile->asArray(),
        ));

        return $response->withHeader('Content-type', 'application/json');
    }
}
