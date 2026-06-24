<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Persist;

use App\Uploads\StagedUploadLocator;
use App\Uploads\UploadClaim;
use RuntimeException;

use function file_exists;
use function is_dir;
use function preg_replace;
use function rmdir;
use function scandir;
use function sprintf;
use function strrpos;
use function strtolower;
use function substr;
use function unlink;

/**
 * Claims hymn uploads into the shared filesAboveWebroot volume, preserving the
 * Craft layout ({slug}/music and {slug}/audio) so migrated and newly uploaded
 * files share one convention and the existing member download route resolves
 * both.
 *
 * Inputs are completed upload handles. File names are derived here rather than
 * trusted from the client: the sheet is "sheet.{ext}" (ext taken from the
 * uploaded file's name) and each practice track is named from its (slugified)
 * title. Practice tracks were validated as MP3 when the upload completed; the
 * sheet may be any type (matching Craft, where the music field allowed all
 * types).
 */
readonly class HymnFileStorage
{
    private const string BASE_PATH = '/var/www/filesAboveWebroot';

    private const string MUSIC_DIR = 'music';

    private const string AUDIO_DIR = 'audio';

    public function __construct(
        private UploadClaim $uploadClaim,
        private StagedUploadLocator $locator,
    ) {
    }

    /** @return string the stored relative path, e.g. "january-2024/music/sheet.pdf" */
    public function saveSheet(string $uploadId, string $slug): string
    {
        return $this->claim(
            uploadId: $uploadId,
            slug: $slug,
            subDir: self::MUSIC_DIR,
            fileName: 'sheet.' . $this->extensionFor(uploadId: $uploadId),
        );
    }

    /** @return string the stored relative path, e.g. "january-2024/audio/full-mix.mp3" */
    public function saveTrack(
        string $uploadId,
        string $slug,
        string $fileNameBase,
    ): string {
        return $this->claim(
            uploadId: $uploadId,
            slug: $slug,
            subDir: self::AUDIO_DIR,
            fileName: $fileNameBase . '.mp3',
        );
    }

    public function deleteAllForSlug(string $slug): void
    {
        if ($slug === '') {
            return;
        }

        $this->deleteRecursive(path: sprintf('%s/%s', self::BASE_PATH, $slug));
    }

    private function claim(
        string $uploadId,
        string $slug,
        string $subDir,
        string $fileName,
    ): string {
        $relativePath = sprintf('%s/%s/%s', $slug, $subDir, $fileName);

        $result = $this->uploadClaim->claimTo(
            uploadId: $uploadId,
            absoluteDestPath: sprintf('%s/%s', self::BASE_PATH, $relativePath),
        );

        if (! $result->success) {
            throw new RuntimeException($result->getMessage());
        }

        return $relativePath;
    }

    private function extensionFor(string $uploadId): string
    {
        $session  = $this->locator->sessionFor(uploadId: $uploadId);
        $fileName = $session->fileName ?? '';

        $dotPosition = strrpos($fileName, '.');

        if ($dotPosition === false) {
            return 'pdf';
        }

        $extension = preg_replace(
            '/[^A-Za-z0-9]+/',
            '',
            substr($fileName, $dotPosition + 1),
        ) ?? '';

        return $extension === '' ? 'pdf' : strtolower($extension);
    }

    private function deleteRecursive(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (! is_dir($path)) {
            unlink($path);

            return;
        }

        $entries = scandir($path);

        foreach ($entries === false ? [] : $entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $this->deleteRecursive(path: sprintf('%s/%s', $path, $entry));
        }

        rmdir($path);
    }
}
