<?php

declare(strict_types=1);

namespace App\InternalMessages;

use App\Uploads\UploadHandle;

// phpcs:disabled SlevomatCodingStandard.Classes.SuperfluousTraitNaming.SuperfluousSuffix

trait AudioValidationEntityTrait
{
    public function audioPathIsFileUpload(): bool
    {
        return UploadHandle::isHandle($this->audioPath);
    }

    public function audioUploadId(): string
    {
        return UploadHandle::fromValue($this->audioPath)->uploadId;
    }

    public function createAudioFileNameForPersistence(): string
    {
        if (! $this->audioPathIsFileUpload()) {
            return $this->audioPath;
        }

        return $this->getAudioFileName();
    }

    public function getAudioFileName(): string
    {
        return $this->slug . '.mp3';
    }

    public function computeAudioFileSize(): int
    {
        return $this->audioFileSize;
    }
}
