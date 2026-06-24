<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Persist;

use App\Messages\Message;
use App\Messages\NewMessage;
use App\Result\Result;
use App\Uploads\UploadClaim;

use function sprintf;

readonly class PersistMessageAudioFile
{
    public function __construct(private UploadClaim $uploadClaim)
    {
    }

    public function persist(Message|NewMessage $message): Result
    {
        // No new upload: an unchanged stored path (or empty) needs no file work.
        if (! $message->audioPathIsFileUpload()) {
            return new Result();
        }

        $absoluteFilePath = sprintf(
            '/var/www/public/uploads/audio/%s',
            $message->getAudioFileName(),
        );

        return $this->uploadClaim->claimTo(
            uploadId: $message->audioUploadId(),
            absoluteDestPath: $absoluteFilePath,
        );
    }
}
