<?php

declare(strict_types=1);

namespace App\Images;

use App\Shared\Files\PublicDirectoryFileSystem;
use App\Shared\Files\RemoteSplFileInfo;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use SplFileInfo;
use Throwable;

use function ltrim;
use function rtrim;

class SourceFileRetriever
{
    public function __construct(
        private GuzzleClient $guzzleClient,
        private PublicDirectoryFileSystem $fileSystem,
    ) {
    }

    /** @var SplFileInfo[] */
    private array $runtimeCache = [];

    public function retrieveInfo(string $pathOrUrl): SplFileInfo
    {
        try {
            if (isset($this->runtimeCache[$pathOrUrl])) {
                return $this->runtimeCache[$pathOrUrl];
            }

            $this->runtimeCache[$pathOrUrl] = $this->tryRetrieveInfo(
                pathOrUrl: $pathOrUrl
            );

            return $this->runtimeCache[$pathOrUrl];
        } catch (Throwable) {
            return new SplFileInfo('');
        }
    }

    /**
     * @throws GuzzleException
     */
    private function tryRetrieveInfo(string $pathOrUrl): SplFileInfo
    {
        if ($this->fileSystem->has($pathOrUrl)) {
            $prefix = (string) $this->fileSystem->getAdapter()->getPathPrefix();

            $prefix = rtrim($prefix, '/');

            $path = ltrim($pathOrUrl, '/');

            return new SplFileInfo(
                $prefix . '/' . $path
            );
        }

        $requestResponse = $this->guzzleClient->get($pathOrUrl);

        $body = $requestResponse->getBody();

        return new RemoteSplFileInfo(
            filename: $pathOrUrl,
            size: (int) $body->getSize(),
            content: (string) $body,
        );
    }
}
