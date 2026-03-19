<?php

declare(strict_types=1);

namespace App\Oauth\RefreshToken;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

class RefreshTokenEntity implements RefreshTokenEntityInterface
{
    use EntityTrait;
    use RefreshTokenTrait;
    // /** @param non-empty-string $identifier */
    // public function __construct(
    //     string $identifier,
    //     AccessTokenEntityInterface $accessToken,
    //     DateTimeImmutable $expiryDateTime,
    // ) {
    //     $this->setIdentifier($identifier);
    //
    //     $this->setAccessToken($accessToken);
    //
    //     $this->setExpiryDateTime($expiryDateTime);
    // }
}
