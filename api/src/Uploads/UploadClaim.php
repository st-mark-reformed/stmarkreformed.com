<?php

declare(strict_types=1);

namespace App\Uploads;

use App\Result\Result;

use function copy;
use function dirname;
use function file_exists;
use function is_dir;
use function mkdir;

/**
 * Moves a completed, staged upload into a feature's final destination path.
 * The staging area lives on a different volume than the final upload targets,
 * so this is a copy + discard rather than an atomic rename.
 *
 * Call sizeFor() before claimTo() when the byte size is needed: claimTo()
 * discards the staging directory (including the manifest) on success.
 */
readonly class UploadClaim
{
    public function __construct(private StagedUploadLocator $locator)
    {
    }

    public function claimTo(string $uploadId, string $absoluteDestPath): Result
    {
        $assembledPath = $this->locator->assembledPathFor(uploadId: $uploadId);

        if (! file_exists($assembledPath)) {
            return new Result(success: false, errors: ['upload' => 'The uploaded file could not be found.']);
        }

        $directory = dirname($absoluteDestPath);

        if (
            ! is_dir($directory) &&
            ! mkdir($directory, 0775, true) &&
            ! is_dir($directory)
        ) {
            return new Result(success: false, errors: ['upload' => 'Unable to create the destination directory.']);
        }

        if (! copy($assembledPath, $absoluteDestPath)) {
            return new Result(success: false, errors: ['upload' => 'Unable to store the uploaded file.']);
        }

        $this->locator->discard(uploadId: $uploadId);

        return new Result();
    }

    public function sizeFor(string $uploadId): int
    {
        $session = $this->locator->sessionFor(uploadId: $uploadId);

        return $session->totalSize ?? 0;
    }
}
