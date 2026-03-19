<?php

declare(strict_types=1);

namespace App\Oauth\Scopes\Entities;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

class EmailVerifiedScope implements ScopeEntityInterface
{
    use ScopeTrait;
    use EntityTrait;

    public function __construct()
    {
        $this->setIdentifier('email_verified');
    }
}
