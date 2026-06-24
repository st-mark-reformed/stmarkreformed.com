<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Persist;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\NewInternalMessage;
use App\Result\Result;
use App\Uploads\UploadClaim;

use function sprintf;

readonly class PersistInternalMessageAudioFile
{
    public function __construct(private UploadClaim $uploadClaim)
    {
    }

    public function persist(InternalMessage|NewInternalMessage $message): Result
    {
        // No new upload: an unchanged stored path (or empty) needs no file work.
        if (! $message->audioPathIsFileUpload()) {
            return new Result();
        }

        $absoluteFilePath = sprintf(
            '/var/www/filesAboveWebroot/internal-audio/%s/%s',
            $message->slug,
            $message->getAudioFileName(),
        );

        return $this->uploadClaim->claimTo(
            uploadId: $message->audioUploadId(),
            absoluteDestPath: $absoluteFilePath,
        );
    }
}
