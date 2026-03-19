<?php

declare(strict_types=1);

namespace App\Oauth\Client;

use Config\RuntimeConfigOptions;
use DateInterval;
use RxAnte\AppBootstrap\RuntimeConfig;

readonly class ClientRepositoryFactory
{
    public function __construct(private RuntimeConfig $config)
    {
    }

    public function create(): ClientRepository
    {
        return new ClientRepository(clientEntities: [
            new ClientEntity(
                identifier: 'y8BwEVCNZpCJbWgit3LL6ctMaevF42dJ',
                name: 'SMRC Website',
                redirectUri: $this->config->getString(
                    RuntimeConfigOptions::SMRC_CLIENT_REDIRECT_URI,
                ),
                isConfidential: true,
                supportedGrantTypes: [
                    'authorization_code',
                    'client_credentials',
                    'refresh_token',
                ],
                clientSecret: $this->config->getString(
                    RuntimeConfigOptions::SMRC_CLIENT_SECRET,
                ),
                accessTokenExpiration: new DateInterval('PT15M'),
            ),
        ]);
    }
}
