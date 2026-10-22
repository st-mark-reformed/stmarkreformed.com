<?php

declare(strict_types=1);

namespace App\Persistence;

use Config\RuntimeConfigOptions;
use PDO;
use RxAnte\AppBootstrap\RuntimeConfig;

use function implode;
use function ini_set;

readonly class ApiPdoFactory
{
    public function __construct(private RuntimeConfig $config)
    {
    }

    public function create(): ApiPdo
    {
        // Bound how long a read waits on MySQL (PHP's default is a day), so a
        // stuck query can't hang a request, a queue job, or a scheduled run.
        // mysqlnd reads this when it connects, so it has to be set first.
        ini_set('mysqlnd.net_read_timeout', '60');

        return new ApiPdo(
            implode(';', [
                'mysql:host=' . $this->config->getString(
                    RuntimeConfigOptions::SMRC_API_DB_HOST,
                ),
                'port=3306',
                'dbname=' . $this->config->getString(
                    RuntimeConfigOptions::SMRC_API_DB_NAME,
                ),
                'charset=utf8',
            ]),
            $this->config->getString(
                RuntimeConfigOptions::SMRC_API_DB_USER,
            ),
            $this->config->getString(
                RuntimeConfigOptions::SMRC_API_DB_PASSWORD,
            ),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 10,
            ],
        );
    }
}
