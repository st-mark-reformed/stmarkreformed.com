<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Config\ContactFormRecipients;
use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;
use Config\SystemFromAddress;
use Psr\Container\ContainerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;

use function assert;
use function implode;

readonly class RegisterBindingsEmail
{
    public static function register(Bindings $bindings): void
    {
        $runtimeConfig = new RuntimeConfig();

        $bindings->addBinding(
            TransportInterface::class,
            static function (
                ContainerInterface $container,
            ): TransportInterface {
                $config = $container->get(RuntimeConfig::class);
                assert($config instanceof RuntimeConfig);

                $userPassParts = [
                    $config->getString(RuntimeConfigOptions::SMTP_USER),
                    $config->getString(
                        RuntimeConfigOptions::SMTP_PASSWORD,
                    ),
                ];

                $dsnParts = [
                    'smtp://',
                    implode(':', $userPassParts),
                    '@',
                    $config->getString(RuntimeConfigOptions::SMTP_HOST),
                    ':',
                    $config->getString(RuntimeConfigOptions::SMTP_PORT),
                ];

                $dsn = implode($dsnParts);

                return Transport::fromDsn($dsn);
            },
        );

        $bindings->addBinding(
            MailerInterface::class,
            $bindings->autowire(Mailer::class),
        );

        $bindings->addBinding(
            SystemFromAddress::class,
            static fn () => new SystemFromAddress(new Address(
                $runtimeConfig->getString(
                    RuntimeConfigOptions::SYSTEM_EMAIL_FROM_ADDRESS,
                ),
                $runtimeConfig->getString(
                    RuntimeConfigOptions::SYSTEM_EMAIL_FROM_NAME,
                ),
            )),
        );

        $bindings->addBinding(
            ContactFormRecipients::class,
            static fn () => ContactFormRecipients::fromEnvString(
                $runtimeConfig->getString(
                    RuntimeConfigOptions::CONTACT_FORM_RECIPIENTS,
                    '',
                ),
            ),
        );
    }
}
