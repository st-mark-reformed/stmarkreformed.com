<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Oauth\OauthPrivateKey;
use App\Oauth\OauthPublicKey;
use RxAnte\AppBootstrap\Dependencies\Bindings;

class Auth
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            OauthPrivateKey::class,
            static fn () => new OauthPrivateKey(
                '/run/secrets/RSA_PRIVATE_KEY',
                keyPermissionsCheck: false,
            ),
        );

        $bindings->addBinding(
            OauthPublicKey::class,
            static fn () => new OauthPublicKey(
                '/run/secrets/RSA_PUBLIC_KEY',
                keyPermissionsCheck: false,
            ),
        );
    }
}
