<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

readonly class DatabaseDumpMapping
{
    /**
     * @param string $appContainer   The container whose environment holds
     *                               the app's credentials for this database
     * @param string $userEnvVar     Env var in that container naming the
     *                               database user
     * @param string $passwordEnvVar Env var in that container holding the
     *                               database user's password
     */
    public function __construct(
        public string $sourceFile,
        public string $targetDatabase,
        public string $appContainer,
        public string $userEnvVar,
        public string $passwordEnvVar,
    ) {
    }
}
