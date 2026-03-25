<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile;

use App\Profiles\ProfileResult;
use App\Responder;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class GetEditProfileResponderFactory
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function create(ProfileResult $result): Responder
    {
        if (! $result->hasProfile) {
            return new GetEditProfileRespondWithNotFound(
                factory: $this->factory,
            );
        }

        return new GetEditProfileRespondWithProfile(
            profile: $result->profile,
            factory: $this->factory,
        );
    }
}
