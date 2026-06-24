<?php

declare(strict_types=1);

namespace App\Uploads;

use function str_starts_with;
use function strlen;
use function substr;

/**
 * A reference to a completed, staged upload that a feature can claim into its
 * final location. Travels through the form payload as "upload:{uploadId}" so it
 * is unambiguously distinguishable from an unchanged, already-stored file path.
 */
readonly class UploadHandle
{
    public const string PREFIX = 'upload:';

    public function __construct(public string $uploadId)
    {
    }

    public static function isHandle(string $value): bool
    {
        return str_starts_with($value, self::PREFIX);
    }

    public static function fromValue(string $value): self
    {
        return new self(uploadId: substr($value, strlen(self::PREFIX)));
    }
}
