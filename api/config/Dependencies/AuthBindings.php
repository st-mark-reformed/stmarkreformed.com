<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use RxAnte\AppBootstrap\RuntimeConfig;
use RxAnte\OAuth\Handlers\Common\OauthPublicKey;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\RedisUserInfoFetchLock;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchLock;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

readonly class AuthBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $config = new RuntimeConfig();

        $bindings->addBinding(
            UserInfoFetchLock::class,
            $bindings->resolveFromContainer(RedisUserInfoFetchLock::class),
        );

        $bindings->addBinding(
            OauthUserInfoRepositoryInterface::class,
            $bindings->resolveFromContainer(RxAnteUserInfoRepository::class),
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
            RxAnteConfig::class,
            static fn () => new RxAnteConfig(
                $config->getString(
                    RuntimeConfigOptions::AUTH_WELL_KNOWN_URL,
                ),
            ),
        );
    }
}
