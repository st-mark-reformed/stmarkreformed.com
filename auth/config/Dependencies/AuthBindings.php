<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Oauth\AccessToken\AccessTokenRepository;
use App\Oauth\AuthCode\AuthCodeRepository;
use App\Oauth\AuthorizationServerFactory;
use App\Oauth\Client\ClientRepository;
use App\Oauth\Client\ClientRepositoryFactory;
use App\Oauth\OauthPrivateKey;
use App\Oauth\OauthPublicKey;
use App\Oauth\RefreshToken\RefreshTokenRepository;
use App\Oauth\Scopes\ScopeRepository;
use Config\RuntimeConfigOptions;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Psr\Container\ContainerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use RxAnte\AppBootstrap\RuntimeConfig;

readonly class AuthBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $config = new RuntimeConfig();

        $bindings->addBinding(
            OauthPrivateKey::class,
            static fn () => new OauthPrivateKey(
                $config->getString(
                    RuntimeConfigOptions::RSA_PRIVATE_KEY,
                ),
            ),
        );

        $bindings->addBinding(
            OauthPublicKey::class,
            static fn () => new OauthPublicKey(
                $config->getString(
                    RuntimeConfigOptions::RSA_PUBLIC_KEY,
                ),
            ),
        );

        $bindings->addBinding(
            AuthorizationServer::class,
            static function (ContainerInterface $di): AuthorizationServer {
                return $di->get(AuthorizationServerFactory::class)->create();
            },
        );

        $bindings->addBinding(
            ScopeRepositoryInterface::class,
            $bindings->resolveFromContainer(ScopeRepository::class),
        );

        $bindings->addBinding(
            ClientRepository::class,
            static function (ContainerInterface $container): ClientRepository {
                $factory = $container->get(ClientRepositoryFactory::class);

                return $factory->create();
            },
        );

        $bindings->addBinding(
            ClientRepositoryInterface::class,
            $bindings->resolveFromContainer(ClientRepository::class),
        );

        $bindings->addBinding(
            AuthCodeRepositoryInterface::class,
            $bindings->resolveFromContainer(AuthCodeRepository::class),
        );

        $bindings->addBinding(
            AccessTokenRepositoryInterface::class,
            $bindings->resolveFromContainer(AccessTokenRepository::class),
        );

        $bindings->addBinding(
            RefreshTokenRepositoryInterface::class,
            $bindings->resolveFromContainer(RefreshTokenRepository::class),
        );
    }
}
