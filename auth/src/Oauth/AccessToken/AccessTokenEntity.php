<?php

declare(strict_types=1);

namespace App\Oauth\AccessToken;

use DateTimeImmutable;
use League\OAuth2\Server\CryptKeyInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

use function array_map;

class AccessTokenEntity implements AccessTokenEntityInterface
{
    use EntityTrait;
    use AccessTokenTrait;
    use TokenEntityTrait;

    /**
     * @param non-empty-string       $identifier
     * @param ScopeEntityInterface[] $scopes
     * @param non-empty-string       $userIdentifier,
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(
        string $identifier,
        CryptKeyInterface $privateKey,
        array $scopes,
        DateTimeImmutable $expiryDateTime,
        string|null $userIdentifier,
        ClientEntityInterface $client,
    ) {
        $this->setIdentifier($identifier);

        $this->setPrivateKey($privateKey);

        array_map(
            function (ScopeEntityInterface $scope): void {
                $this->addScope($scope);
            },
            $scopes,
        );

        $this->setExpiryDateTime($expiryDateTime);

        if ($userIdentifier !== null) {
            $this->setUserIdentifier($userIdentifier);
        }

        $this->setClient($client);
    }
}
