<?php

declare(strict_types=1);

namespace App\Uploads;

/**
 * Resolves the byte size of an upload handle from its staging session, so a
 * feature can record the size on its row before the file is claimed and the
 * session discarded. Returns 0 for anything that is not an upload handle (an
 * unchanged stored path, or an empty value).
 */
readonly class UploadHandleSize
{
    public function __construct(private StagedUploadLocator $locator)
    {
    }

    public function forValue(string $value): int
    {
        if (! UploadHandle::isHandle($value)) {
            return 0;
        }

        $session = $this->locator->sessionFor(
            uploadId: UploadHandle::fromValue($value)->uploadId,
        );

        return $session->totalSize ?? 0;
    }
}
