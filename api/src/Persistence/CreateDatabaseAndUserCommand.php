<?php

declare(strict_types=1);

namespace App\Persistence;

use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function implode;

readonly class CreateDatabaseAndUserCommand
{
    public static function register(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            'persistence:create-db',
            self::class,
        );
    }

    public function __construct(
        private RootPdo $pdo,
        private RuntimeConfig $config,
    ) {
    }

    public function __invoke(): void
    {
        $this->ensureDbExists();
        $this->ensureUserExists();
    }

    private function ensureDbExists(): void
    {
        $dbName = $this->config->getString(
            RuntimeConfigOptions::API_DB_NAME,
        );

        $this->pdo->exec(
            'CREATE DATABASE IF NOT EXISTS `' . $dbName . '`',
        );
    }

    private function ensureUserExists(): void
    {
        $dbUser = $this->config->getString(
            RuntimeConfigOptions::API_DB_USER,
        );

        $dbPass = $this->config->getString(
            RuntimeConfigOptions::API_DB_PASSWORD,
        );

        $this->pdo->exec(implode(' ', [
            "CREATE USER IF NOT EXISTS '" . $dbUser . "'@'%'",
            "IDENTIFIED BY '" . $dbPass . "'",
        ]));

        $this->pdo->exec(implode(' ', [
            'GRANT ALL on ' . $dbUser . '.*',
            "to '" . $dbUser . "'@'%'",
        ]));
    }
}
