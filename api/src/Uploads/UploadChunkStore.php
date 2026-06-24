<?php

declare(strict_types=1);

namespace App\Uploads;

use RuntimeException;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function is_dir;
use function json_decode;
use function json_encode;
use function mkdir;
use function preg_match;
use function rmdir;
use function scandir;
use function unlink;

/**
 * The only class that touches the in-progress upload staging area on the shared
 * api-storage-volume. Centralizes the directory/manifest/chunk file handling
 * that was previously duplicated across the per-feature storage classes.
 */
readonly class UploadChunkStore
{
    private const string BASE_PATH = '/var/www/storage/uploads';

    public function createSession(UploadSession $session): void
    {
        $directory = $this->sessionDir($session->uploadId);

        $this->ensureDir($directory);

        file_put_contents(
            $directory . '/manifest.json',
            (string) json_encode($session->toManifestArray()),
        );
    }

    public function readSession(string $uploadId): UploadSession|null
    {
        $manifestPath = $this->sessionDir($uploadId) . '/manifest.json';

        if (! file_exists($manifestPath)) {
            return null;
        }

        $raw = file_get_contents($manifestPath);

        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return null;
        }

        return UploadSession::fromManifestArray($data);
    }

    public function writeChunk(string $uploadId, int $index, string $bytes): void
    {
        if (! is_dir($this->sessionDir($uploadId))) {
            throw new RuntimeException('Upload session does not exist.');
        }

        file_put_contents($this->chunkPath($uploadId, $index), $bytes);
    }

    public function receivedChunks(string $uploadId): ReceivedChunkIndices
    {
        $directory = $this->sessionDir($uploadId);

        if (! is_dir($directory)) {
            return new ReceivedChunkIndices(items: []);
        }

        $files = scandir($directory);

        if ($files === false) {
            return new ReceivedChunkIndices(items: []);
        }

        $indices = [];

        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.part$/', $file, $matches) !== 1) {
                continue;
            }

            $indices[] = (int) $matches[1];
        }

        return new ReceivedChunkIndices(items: $indices);
    }

    public function chunkPath(string $uploadId, int $index): string
    {
        return $this->sessionDir($uploadId) . '/' . $index . '.part';
    }

    public function assembledPath(string $uploadId): string
    {
        return $this->sessionDir($uploadId) . '/assembled.bin';
    }

    /** @return string[] */
    public function listUploadIds(): array
    {
        if (! is_dir(self::BASE_PATH)) {
            return [];
        }

        $entries = scandir(self::BASE_PATH);

        if ($entries === false) {
            return [];
        }

        $ids = [];

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (! is_dir(self::BASE_PATH . '/' . $entry)) {
                continue;
            }

            if (preg_match('/^[a-f0-9\-]{36}$/i', $entry) !== 1) {
                continue;
            }

            $ids[] = $entry;
        }

        return $ids;
    }

    public function deleteSession(string $uploadId): void
    {
        $directory = $this->sessionDir($uploadId);

        if (! is_dir($directory)) {
            return;
        }

        $files = scandir($directory);

        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                unlink($directory . '/' . $file);
            }
        }

        rmdir($directory);
    }

    private function sessionDir(string $uploadId): string
    {
        return self::BASE_PATH . '/' . $this->safeId($uploadId);
    }

    /**
     * Guards against path traversal: the upload id always comes from a URL
     * segment, so it must be a plain UUID before it is used in a path.
     */
    private function safeId(string $uploadId): string
    {
        if (preg_match('/^[a-f0-9\-]{36}$/i', $uploadId) !== 1) {
            throw new RuntimeException('Invalid upload id.');
        }

        return $uploadId;
    }

    private function ensureDir(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create upload directory.');
        }
    }
}
