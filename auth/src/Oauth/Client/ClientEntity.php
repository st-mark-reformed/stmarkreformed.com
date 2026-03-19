<?php

declare(strict_types=1);

namespace App\Oauth\Client;

use DateInterval;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

use function array_map;
use function in_array;
use function is_array;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait;
    use ClientTrait;

    /**
     * @param non-empty-string $identifier
     * @param string|string[]  $redirectUri
     * @param string[]         $supportedGrantTypes
     */
    public function __construct(
        string $identifier,
        string $name,
        string|array $redirectUri,
        bool $isConfidential,
        protected array $supportedGrantTypes,
        protected string $clientSecret,
        protected DateInterval $accessTokenExpiration,
    ) {
        $this->setIdentifier($identifier);

        $this->name = $name;

        $this->redirectUri = $redirectUri;

        $this->isConfidential = $isConfidential;

        if (is_array($redirectUri)) {
            array_map(
                static fn (string $uri) => $uri,
                $redirectUri,
            );
        }

        array_map(
            static fn (string $grant) => $grant,
            $supportedGrantTypes,
        );
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function supportsGrantType(string $grantType): bool
    {
        return in_array(
            $grantType,
            $this->supportedGrantTypes,
            true,
        );
    }

    public function accessTokenExpiration(): DateInterval
    {
        return $this->accessTokenExpiration;
    }
}
