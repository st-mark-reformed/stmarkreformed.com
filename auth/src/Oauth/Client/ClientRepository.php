<?php

declare(strict_types=1);

namespace App\Oauth\Client;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

use function array_find;
use function array_map;
use function array_values;

readonly class ClientRepository implements ClientRepositoryInterface
{
    /** @var ClientEntity[] */
    private array $clientEntities;

    /** @param ClientEntity[] $clientEntities */
    public function __construct(array $clientEntities)
    {
        $this->clientEntities = array_values(array_map(
            static fn (ClientEntity $c) => $c,
            $clientEntities,
        ));
    }

    public function getClientEntity(
        string $clientIdentifier,
    ): ClientEntityInterface|null {
        return array_find(
            $this->clientEntities,
            static fn (
                ClientEntity $client,
            ) => $client->getIdentifier() === $clientIdentifier,
        );
    }

    public function validateClient(
        string $clientIdentifier,
        string|null $clientSecret,
        string|null $grantType,
    ): bool {
        $grantType ??= '';

        $client = $this->getClientEntity($clientIdentifier);

        if (! ($client instanceof ClientEntity)) {
            return false;
        }

        if (! $client->supportsGrantType($grantType)) {
            return false;
        }

        return $clientSecret === $client->getClientSecret();
    }
}
