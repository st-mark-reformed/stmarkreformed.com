<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;
use Config\SigningCertificate;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Container\ContainerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use RxAnte\OAuth\Handlers\Auth0\Auth0Config;
use RxAnte\OAuth\Handlers\Auth0\Auth0LeagueOauthProviderConfig;
use RxAnte\OAuth\Handlers\Auth0\Auth0LeagueOauthProviderFactory;
use RxAnte\OAuth\Handlers\Auth0\Auth0UserInfoRepository;
use RxAnte\OAuth\Handlers\Auth0\TokenRefresh\GetRefreshedAccessTokenFromAuth0;
use RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RedisRefreshLock;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock;
use RxAnte\OAuth\TokenRepository\TokenRepositoryConfig;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

use function assert;

readonly class RegisterBindingsAuthentication
{
    public static function register(Bindings $bindings): void
    {
        $bindings->addBinding(
            OauthUserInfoRepositoryInterface::class,
            $bindings->resolveFromContainer(
                Auth0UserInfoRepository::class,
            ),
        );

        $bindings->addBinding(
            RefreshLock::class,
            $bindings->resolveFromContainer(RedisRefreshLock::class),
        );

        $bindings->addBinding(
            Auth0Config::class,
            static function (ContainerInterface $container): Auth0Config {
                $runtimeConfig = $container->get(RuntimeConfig::class);
                assert($runtimeConfig instanceof RuntimeConfig);

                $signingCertificate = $container->get(SigningCertificate::class);
                assert($signingCertificate instanceof SigningCertificate);

                return new Auth0Config(
                    userInfoUrl: $runtimeConfig->getString(
                        RuntimeConfigOptions::AUTH0_USER_INFO_URL,
                    ),
                    wellKnownUrl: $runtimeConfig->getString(
                        RuntimeConfigOptions::AUTH0_WELL_KNOWN_URL,
                    ),
                    signingCertificate: $signingCertificate->get(),
                );
            },
        );

        $bindings->addBinding(
            Auth0LeagueOauthProviderConfig::class,
            static function (
                ContainerInterface $container,
            ): Auth0LeagueOauthProviderConfig {
                $runtimeConfig = $container->get(RuntimeConfig::class);
                assert($runtimeConfig instanceof RuntimeConfig);

                return new Auth0LeagueOauthProviderConfig(
                    clientId: $runtimeConfig->getString(
                        RuntimeConfigOptions::AUTH0_CLIENT_ID,
                    ),
                    clientSecret: $runtimeConfig->getString(
                        RuntimeConfigOptions::AUTH0_CLIENT_SECRET,
                    ),
                    callbackDomain: $runtimeConfig->getString(
                        RuntimeConfigOptions::AUTH0_CALLBACK_DOMAIN,
                    ),
                    audience: 'smrc_website',
                );
            },
        );

        $bindings->addBinding(
            AbstractProvider::class,
            static function (ContainerInterface $container): AbstractProvider {
                $factory = $container->get(Auth0LeagueOauthProviderFactory::class);
                assert($factory instanceof Auth0LeagueOauthProviderFactory);

                return $factory->create();
            },
        );

        $bindings->addBinding(
            TokenRepositoryConfig::class,
            static function (): TokenRepositoryConfig {
                return new TokenRepositoryConfig(
                /**
                 * 4800 seconds is 80 minutes, which is how long refresh
                 * tokens are set for in FusionAuth
                 */
                    expireInSeconds: 4800,
                );
            },
        );

        $bindings->addBinding(
            GetRefreshedAccessToken::class,
            $bindings->resolveFromContainer(
                GetRefreshedAccessTokenFromAuth0::class,
            ),
        );
    }
}
