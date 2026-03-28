<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile\GetEditProfile;

use App\Profiles\ProfileResult;
use App\Responder;
use App\RespondWithJson;
use App\RespondWithNotFound;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class EditProfileResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(ProfileResult $result): Responder
    {
        if (! $result->hasProfile) {
            return new RespondWithNotFound(
                factory: $this->factory,
                message: 'Profile not found',
            );
        }

        return new RespondWithJson(
            entity: $result->profile,
            factory: $this->factory,
        );
    }
}
