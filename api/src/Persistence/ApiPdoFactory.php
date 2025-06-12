<?php

declare(strict_types=1);

namespace App\Persistence;

use Config\RuntimeConfig;
use Config\RuntimeConfigOptions;
use PDO;

use function implode;

readonly class ApiPdoFactory
{
    public function __construct(private RuntimeConfig $config)
    {
    }

    public function create(): ApiPdo
    {
        return new ApiPdo(
            implode(';', [
                'mysql:host=' . $this->config->getString(
                    RuntimeConfigOptions::API_DB_HOST,
                ),
                'port=' . $this->config->getString(
                    RuntimeConfigOptions::API_DB_PORT,
                ),
                'dbname=' . $this->config->getString(
                    RuntimeConfigOptions::API_DB_NAME,
                ),
                'charset=utf8',
            ]),
            $this->config->getString(
                RuntimeConfigOptions::API_DB_USER,
            ),
            $this->config->getString(
                RuntimeConfigOptions::API_DB_PASSWORD,
            ),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
    }
}
