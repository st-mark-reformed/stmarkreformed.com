<?php

declare(strict_types=1);

namespace App\Oauth\Authorize;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntityForLeagueOauth implements UserEntityInterface
{
    use EntityTrait;

    /** @param non-empty-string $identifier */
    public function __construct(string $identifier)
    {
        $this->setIdentifier($identifier);
    }
}
