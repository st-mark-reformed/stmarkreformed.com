<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Persist;

use App\InternalMessages\InternalMessage;
use App\InternalMessages\NewInternalMessage;
use App\Result\Result;
use Throwable;

readonly class PersistInternalMessageAudioFile
{
    public function __construct(private InternalMessageAudioFileStorage $storage)
    {
    }

    public function persist(InternalMessage|NewInternalMessage $message): Result
    {
        if (! $message->audioPathIsValidFileUpload()) {
            if ($message->audioPathIsFileUpload()) {
                return new Result(
                    success: false,
                    errors: ['audioPath' => 'Audio file is not a valid MP3.'],
                );
            }

            return new Result();
        }

        try {
            $this->storage->save(
                base64Audio: $message->audioPath,
                slug: $message->slug,
                fileName: $message->getAudioFileName(),
            );
        } catch (Throwable $error) {
            return new Result(
                success: false,
                errors: ['audioPath' => $error->getMessage()],
            );
        }

        return new Result();
    }
}
