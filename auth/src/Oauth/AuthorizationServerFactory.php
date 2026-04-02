<?php

declare(strict_types=1);

namespace App\Oauth;

use App\Oauth\Grants\CustomAuthCodeGrant;
use Config\RuntimeConfigOptions;
use DateInterval;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use RxAnte\AppBootstrap\RuntimeConfig;

readonly class AuthorizationServerFactory
{
    private DateInterval $refreshTokenTTL;

    public function __construct(
        private RuntimeConfig $runtimeConfig,
        private OauthPrivateKey $oauthPrivateKey,
        private ScopeRepositoryInterface $scopeRepository,
        private ClientRepositoryInterface $clientRepository,
        private AuthCodeRepositoryInterface $authCodeRepository,
        private AccessTokenRepositoryInterface $accessTokenRepository,
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {
        $this->refreshTokenTTL = new DateInterval('PT80M');
    }

    public function create(): AuthorizationServer
    {
        $server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            $this->oauthPrivateKey,
            // Created from vendor/bin/generate-defuse-key
            Key::loadFromAsciiSafeString(
                $this->runtimeConfig->getString(
                    RuntimeConfigOptions::OAUTH_ENCRYPTION_KEY,
                ),
            ),
        );

        $this->enableAuthCodeGrant($server);

        $this->enableRefreshTokenGrant($server);

        $this->enabledClientCredentialsGrant($server);

        return $server;
    }

    private function enableAuthCodeGrant(AuthorizationServer $server): void
    {
        $grant = new CustomAuthCodeGrant(
            authCodeRepository: $this->authCodeRepository,
            refreshTokenRepository: $this->refreshTokenRepository,
            authCodeTTL: new DateInterval('PT10M'),
        );

        $grant->setRefreshTokenTTL($this->refreshTokenTTL);

        $server->enableGrantType(
            $grant,
            new DateInterval('PT15M'),
        );
    }

    private function enableRefreshTokenGrant(AuthorizationServer $server): void
    {
        $grant = new RefreshTokenGrant(
            $this->refreshTokenRepository,
        );

        $grant->setRefreshTokenTTL($this->refreshTokenTTL);

        $server->enableGrantType($grant);
    }

    private function enabledClientCredentialsGrant(
        AuthorizationServer $server,
    ): void {
        $server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval('PT15M'),
        );
    }
}
